<?php
namespace backend\controllers;

use common\models\ar\TrnNotif;
use common\models\ar\TrnSc;
use common\models\ar\TrnScSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApprovalScController extends Controller
{
    /**
     * Lists all Sc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnScSearch([]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->andWhere(['>=', 'trn_sc.date', '2019-01-01']);

        $paramRole = Yii::$app->params['rbac_roles'];
        $paramRoleDirut = $paramRole['dirut'];
        $paramRoleDirMar = $paramRole['dir_marketing'];
        $paramRoleMgrMarketing = $paramRole['mgr_marketing'];

        $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $userRole = ArrayHelper::toArray($userRole);
        $userRole = array_keys($userRole);

        //BaseVarDumper::dump($userRole, 10, true);Yii::$app->end();

        if(in_array($paramRoleDirut, $userRole) || in_array($paramRoleDirMar, $userRole)){
            $dataProvider->query
                ->andWhere(['trn_sc.apv_dir_at'=>null])
                ->andWhere(['trn_sc.status'=>[TrnSc::STATUS_POSTED, TrnSc::STATUS_APV_MGR]])
            ;
        }else if(in_array($paramRoleMgrMarketing, $userRole)){
            $dataProvider->query->andWhere(['trn_sc.status'=>[TrnSc::STATUS_POSTED]])->andWhere(['trn_sc.apv_mgr_at'=>null]);
        }else{
            //$dataProvider->query->where('0=1');
            $dataProvider->query->andWhere(['trn_sc.status'=>[TrnSc::STATUS_POSTED]]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sc model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReject($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if(!in_array($model->status, [$model::STATUS_POSTED, $model::STATUS_APV_MGR])){
                throw new ForbiddenHttpException('SC ini belum diposting, penolakan tidak bisa diproses.');
            }

            $note = Yii::$app->request->post('reject_note');
            if(empty($note)){
                throw new ForbiddenHttpException('Catatan penolakan harus diisi.');
            }

            $userId = Yii::$app->user->id;
            $noteDir = Json::decode($model->reject_note_dir);
            $noteMgr = Json::decode($model->reject_note_mgr);
            $date = date('Y-m-d H:i:s');

            if($userId == $model->direktur_id){
                $noteDir[] = [
                    'date_time' => $date,
                    'note'=> $note
                ];
                $model->apv_dir_at = null;
                $model->reject_note_dir = Json::encode($noteDir);
                $model->apv_mgr_at = null;

                $saveAttr = ['status', 'posted_at', 'apv_mgr_at', 'apv_dir_at', 'reject_note_dir'];
            }else if ($userId == $model->manager_id){
                $noteMgr[] = [
                    'date_time' => $date,
                    'note'=> $note
                ];
                $model->apv_mgr_at = null;
                $model->reject_note_mgr = Json::encode($noteMgr);

                $model->apv_dir_at = null;
                $saveAttr = ['status', 'posted_at', 'apv_mgr_at', 'reject_note_mgr'];
            }else{
                throw new ForbiddenHttpException('selain direktur atau manager tidak diizinkan.');
            }

            $model->status = $model::STATUS_DRAFT;
            $model->posted_at = null;
            $model->save(false, $saveAttr);

            //tandai task sudah dibaca
            $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-sc/view', 'id'=>$model->id]);
            /* @var $tasks TrnNotif[]*/
            $tasks = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false])->all();
            foreach ($tasks as $task) {
                $task->read = true;
                $task->save(false, ['read']);
            }
            //tandai task sudah dibaca

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return bool
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function actionApprove($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);

            if(!in_array($model->status, [$model::STATUS_POSTED, $model::STATUS_APV_MGR])){
                throw new ForbiddenHttpException('SC ini belum diposting, persetujuan tidak bisa diproses.');
            }

            $userId = Yii::$app->user->id;
            $now = time();
            $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-sc/view', 'id'=>$model->id]);

            if($userId == $model->direktur_id){
                $model->status = $model::STATUS_APPROVED;
                $model->apv_dir_at = $now;
                if($model->apv_mgr_at == null){
                    $model->apv_mgr_at = $now;
                }

                //tandai task sudah dibaca direktur
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->direktur_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca direktur

                //tandai task sudah dibaca manager
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->manager_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca manager
            }elseif ($userId == $model->manager_id){
                $model->status = $model::STATUS_APV_MGR;
                $model->apv_mgr_at = $now;

                //tandai task sudah dibaca manager
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->manager_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca manager
            }else{
                throw new ForbiddenHttpException('selain direktur atau manager tidak diizinkan.');
            }

            if($model->status == $model::STATUS_APPROVED){
                //Memberi nomor urut dan nomor pada SC
                $model->setNoUrut();
                $model->setNoSc();

                $transaction = $model::getDb()->beginTransaction();
                try{
                    if(!$flag = $model->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'error saving data 1.');
                    }

                    //Memberi nomor urut dan nomor pada LOA AGEN
                    foreach ($model->trnScAgens as $trnScAgen) {
                        $trnScAgen->setNoUrut();
                        $trnScAgen->setNoLoa();

                        if(!$flag = $trnScAgen->save(false, ['no_urut', 'no'])){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Approval SC gagal. (kegagalan setup no & no_urut LOA.)');
                        }
                    }

                    //Memberi nomor urut dan nomor pada Order Greige
                    $i = 1;
                    foreach ($model->trnScGreiges as $trnScGreige) {
                        $trnScGreige->setNoOrderGreige($model->date, $model->tipe_kontrak, $i, $model->jenis_order, $model->no_urut);

                        if(!$flag = $trnScGreige->save(false, ['no_urut_order_greige', 'no_order_greige'])){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Approval SC gagal. (kegagalan setup no & no_urut Order Greige.)');
                        }

                        $i++;
                    }

                    if($flag){
                        $transaction->commit();
                        return true;
                    }
                }catch(\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }else{
                $model->save(false);
                return true;
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the Sc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TrnSc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnSc::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}