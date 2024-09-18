<?php
namespace backend\controllers;

use common\models\ar\TrnMo;
use common\models\ar\TrnMoSearch;
use common\models\ar\TrnNotif;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApprovalMoController extends Controller
{
    /**
     * Lists all Sc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMoSearch(['status'=>TrnMo::STATUS_POSTED, 'approval_id'=>Yii::$app->user->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->andWhere(['>=', 'trn_mo.date', '2019-01-01']);

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
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReject($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Status MO ini tidak valid, penolakan tidak bisa diproses.');
            }

            if(Yii::$app->user->id != $model->approval_id){
                throw new ForbiddenHttpException('Anda tidak berwenang untuk melakukan persetujuan ini.');
            }

            $rejectNote = [
                'date_time' => date('Y-m-d H:i:s'),
                'note'=> Yii::$app->request->post('data')
            ];

            if($model->reject_notes !== null){
                $note = Json::decode($model->reject_notes);
                $note[] = $rejectNote;
            }else{
                $note = [$rejectNote];
            }

            $model->posted_at = null;
            $model->approved_at = null;
            $model->status = $model::STATUS_DRAFT;
            $model->reject_notes = Json::encode($note);

            if($model->save(false, ['posted_at', 'approved_at', 'status', 'reject_notes']) !== false){
                //tandai task sudah dibaca
                $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-mo/view', 'id'=>$model->id]);
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->approval_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca

                return true;
            }else{
                throw new HttpException(500, 'Gagal menolak MO.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionApprove($id)
    {
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Status MO ini tidak valid, persetujuan tidak bisa diproses.');
            }

            $model->approved_at = time();
            $model->status = $model::STATUS_APPROVED;
            $model->jenis_gudang = Yii::$app->request->post('formData');
            $model->setNoMO();

            if($model->save(false, ['approved_at', 'status', 'jenis_gudang', 'no', 'no_urut'])){
                //tandai task sudah dibaca
                $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-mo/view', 'id'=>$model->id]);
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->approval_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca

                //close otomatis sc_greige & sc dilakukan via cron job
                return true;
            }else{
                throw new HttpException(500, 'Gagal approval MO.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the Mo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TrnMo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}