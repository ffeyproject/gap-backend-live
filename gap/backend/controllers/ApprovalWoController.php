<?php
namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnNotif;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApprovalWoController extends Controller
{
    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->andWhere(['>=', 'trn_wo.date', '2019-01-01']);

        $paramRole = Yii::$app->params['rbac_roles'];
        $paramRoleMengetahui = $paramRole['kabag_pmc'];
        $paramRoleStaffMarketing = $paramRole['marketing'];

        $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $userRole = ArrayHelper::toArray($userRole);
        $userRole = array_keys($userRole);

        if(in_array($paramRoleMengetahui, $userRole)){
            $dataProvider->query->andWhere(['trn_wo.status' => [TrnWo::STATUS_POSTED, TrnWo::STATUS_APV_MARKETING]]);
        }else if(in_array($paramRoleStaffMarketing, $userRole)){
            $dataProvider->query->andWhere(['trn_wo.status' => [TrnWo::STATUS_POSTED, TrnWo::STATUS_APV_MENNGETAHUI]])->andWhere(['trn_wo.apv_marketing_at' => null]);
        }else{
            $dataProvider->query->where('0=1');
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnWo model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $mo = $model->mo;
        $greige = $model->greige;

        switch ($mo->jenis_gudang){
            case TrnStockGreige::JG_WIP:
                $stockM = $greige->stock_wip;
                $bookedM = $greige->booked_wip;
                $stockLabel = 'stock WIP';
                $bookkLabel = 'booked WIP';
                break;
            case TrnStockGreige::JG_PFP:
                $stockM = $greige->stock_pfp;
                $bookedM = $greige->booked_pfp;
                $stockLabel = 'stock PFP';
                $bookkLabel = 'booked PFP';
                break;
            case TrnStockGreige::JG_EX_FINISH:
                $stockM = $greige->stock_ef;
                $bookedM = $greige->booked_ef;
                $stockLabel = 'stock Ex Finish';
                $bookkLabel = 'booked Ex Finish';
                break;
            default:
                $stockM = $greige->stock;
                $bookedM = $greige->booked;
                $stockLabel = 'stock';
                $bookkLabel = 'booked';
        }

        $avM = $stockM - $bookedM;

        return $this->render('view', [
            'model' => $model,
            'stockM' => $stockM,
            'bookedM' => $bookedM,
            'stockLabel' => $stockLabel,
            'bookkLabel' => $bookkLabel,
            'avM' => $avM
        ]);
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReject($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if(!in_array($model->status, [$model::STATUS_POSTED, $model::STATUS_APV_MARKETING, $model::STATUS_APV_MENNGETAHUI])){
                throw new ForbiddenHttpException('Status WO ini tidak valid, persetujuan tidak bisa diproses.');
            }

            $date = date('Y-m-d H:i:s');

            $rejectNote = [
                'date_time' => $date,
                'note'=> Yii::$app->request->post('reject_note')
            ];

            $paramRole = Yii::$app->params['rbac_roles'];
            $paramRoleKabagPmc = $paramRole['kabag_pmc'];
            $paramRoleMarketing = $paramRole['marketing'];

            $userId = Yii::$app->user->id;
            $userRole = Yii::$app->authManager->getRolesByUser($userId);
            $userRole = ArrayHelper::toArray($userRole);
            $userRole = array_keys($userRole);

            $isKabagPmc = in_array($paramRoleKabagPmc, $userRole);
            $isMarketing = in_array($paramRoleMarketing, $userRole);

            $invalidRole = $isKabagPmc && $isMarketing;
            if($invalidRole){
                throw new ForbiddenHttpException('Anda memliliki akses sebagai Kabag PMC dan Marketing, Anda hanya boleh berperan sebagai salah satunya untuk bisa menolak WO ini, proses penolakan tidak bisa dilanjutkan.');
            }

            $noteMengetahui = Json::decode($model->reject_note_mengetahui);
            $noteMarketing = Json::decode($model->reject_note_marketing);

            if($isKabagPmc){
                if($userId != $model->mengetahui_id){
                    throw new ForbiddenHttpException('Anda bukan Kabag PMC yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $noteMengetahui[] = $rejectNote;

                $model->apv_mengetahui_at = null;
                $model->apv_marketing_at = null;
                $model->reject_note_mengetahui = Json::encode($noteMengetahui);
                $model->status = $model::STATUS_DRAFT;

                $model->save(false, ['apv_mengetahui_at', 'apv_marketing_at', 'reject_note_mengetahui', 'status']);

                //tandai task sudah dibaca
                $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-wo/view', 'id'=>$model->id]);
                /* @var $tasks TrnNotif[]*/
                $tasks = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false])->all();
                foreach ($tasks as $task) {
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca

                return true;
            }else if($isMarketing){
                if($userId != $model->sc->marketing_id){
                    throw new ForbiddenHttpException('Anda bukan Marketing yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $noteMarketing[] = $rejectNote;

                $model->apv_marketing_at = null;
                $model->apv_mengetahui_at = null;
                $model->reject_note_marketing = Json::encode($noteMarketing);
                $model->status = $model::STATUS_DRAFT;

                $noteMarketing[] = [
                    'date_time' => $date,
                    'note'=> 'Ditolak oleh '.$model->marketingName
                ];

                $model->save(false, ['apv_marketing_at', 'apv_mengetahui_at', 'reject_note_marketing', 'status']);

                //tandai task sudah dibaca
                $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-wo/view', 'id'=>$model->id]);
                /* @var $tasks TrnNotif[]*/
                $tasks = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false])->all();
                foreach ($tasks as $task) {
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca

                return true;
            }else{
                throw new ForbiddenHttpException('Anda bukan Kabag PMC ataupun Marketing, proses approval tidak bisa dilanjutkan.');
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
     * @throws \Throwable
     */
    public function actionApprove($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if(!in_array($model->status, [$model::STATUS_POSTED, $model::STATUS_APV_MARKETING, $model::STATUS_APV_MENNGETAHUI])){
                throw new ForbiddenHttpException('Status WO ini tidak valid, persetujuan tidak bisa diproses.');
            }

            $now = time();

            $userId = Yii::$app->user->id;

            $paramRole = Yii::$app->params['rbac_roles'];
            $paramRoleKabagPmc = $paramRole['kabag_pmc'];
            $paramRoleMarketing = $paramRole['marketing'];

            $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            $userRole = ArrayHelper::toArray($userRole);
            $userRole = array_keys($userRole);

            $isKabagPmc = in_array($paramRoleKabagPmc, $userRole);
            $isMarketing = in_array($paramRoleMarketing, $userRole);
            $invalidRole = $isKabagPmc && $isMarketing;

            if($invalidRole){
                throw new ForbiddenHttpException('Anda memliliki akses sebagai Kabag PMC dan Marketing, Anda hanya boleh berperan sebagai salah satunya untuk bisa menyetujui WO ini, proses approval tidak bisa dilanjutkan.');
            }

            if(!$isKabagPmc && !$isMarketing){
                throw new ForbiddenHttpException('Anda bukan Kabag PMC ataupun Marketing, proses approval tidak bisa dilanjutkan.');
            }

            $link = Yii::$app->urlManager->createAbsoluteUrl(['/approval-wo/view', 'id'=>$model->id]);

            if($isKabagPmc){
                if($userId != $model->mengetahui_id){
                    throw new ForbiddenHttpException('Anda bukan Kabag PMC yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $model->apv_mengetahui_at = $now;
                if($model->status == $model::STATUS_APV_MARKETING){
                    $model->status = $model::STATUS_APPROVED;
                }else $model->status = $model::STATUS_APV_MENNGETAHUI;

                //tandai task sudah dibaca
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->mengetahui_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
                //tandai task sudah dibaca
            }else if($isMarketing){
                if($userId != $model->sc->marketing_id){
                    throw new ForbiddenHttpException('Anda bukan Marketing yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $model->apv_marketing_at = $now;
                if($model->status == $model::STATUS_APV_MENNGETAHUI){
                    $model->status = $model::STATUS_APPROVED;
                }else $model->status = $model::STATUS_APV_MARKETING;

                //tandai task sudah dibaca
                /* @var $task TrnNotif*/
                $task = TrnNotif::find()->where(['link'=>$link, 'type'=>TrnNotif::TYPE_TASK, 'read'=>false, 'user_id'=>$model->marketing_id])->one();
                if(!empty($task)){
                    $task->read = true;
                    $task->save(false, ['read']);
                }
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }

            if($model->status == $model::STATUS_APPROVED){
                $model->setNoWo();
            }

            if($model->jenis_order == TrnSc::JENIS_ORDER_FRESH_ORDER){
                //validasi stock greige jika fresh order
                if($model->validasi_stock){
                    // validasi hanya dilakukan jika kolom "validasi_stock" true

                    $mo = $model->mo;
                    $greige = $model->greige;
                    $greigeGroup = $greige->group;
                    $totalColorsBatch = $model->colorQty;
                    $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

                    switch ($mo->jenis_gudang){
                        case TrnStockGreige::JG_WIP:
                            if(($greige->stock_wip - $greige->booked_wip) < $totalColorsMeter){
                                throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                            }
                            break;
                        case TrnStockGreige::JG_PFP:
                            if(($greige->stock_pfp - $greige->booked_pfp) < $totalColorsMeter){
                                throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                            }
                            break;
                        case TrnStockGreige::JG_EX_FINISH:
                            if(($greige->stock_ef - $greige->booked_ef) < $totalColorsMeter){
                                throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                            }
                            break;
                        case TrnStockGreige::JG_FRESH:
                            if($greige->available < $totalColorsMeter){
                                throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                            }

                            if($model->status == $model::STATUS_APPROVED){
                                // lakukan perubahan data di mst_greige hanya jika wo sudah di approve oleh supervisor ( wo sudah ada nomor nya / sudah status approved)
                                $transaction = Yii::$app->db->beginTransaction();
                                try {
                                    if(!$model->save(false)){
                                        $transaction->rollBack();
                                        throw new HttpException(500, 'Gagal menyimpan data, coba lagi.');
                                    }

                                    //catat perubahan stock available dan booked_wo hanya jika fresh order
                                    Yii::$app->db->createCommand()->update(
                                        MstGreige::tableName(),
                                        [
                                            'available' => new \yii\db\Expression('available - ' . $totalColorsMeter),
                                            'booked_wo' => new \yii\db\Expression('booked_wo + ' . $totalColorsMeter)
                                        ],
                                        ['id'=>$greige->id]
                                    )->execute();

                                    $transaction->commit();
                                    return true;
                                }catch (\Throwable $t){
                                    $transaction->rollBack();
                                    throw $t;
                                }
                            }
                            break;
                        default:
                            // jenis gudang lainnya
                            if(($greige->stock - $greige->booked) < $totalColorsMeter){
                                throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                            }
                    }
                }
            }

            $model->save(false);

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the TrnWo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TrnWo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnWo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}