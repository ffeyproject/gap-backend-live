<?php
namespace backend\controllers;

use backend\components\Converter;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use http\Exception\InvalidArgumentException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApprovalWoMarketingController extends Controller
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
    public function actionReject($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Status WO tidak valid, penolakan tidak bisa diproses.');
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
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws HttpException
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

            if($isKabagPmc){
                if($userId != $model->mengetahui_id){
                    throw new ForbiddenHttpException('Anda bukan Kabag PMC yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $model->apv_mengetahui_at = $now;
                if($model->status == $model::STATUS_APV_MARKETING){
                    $model->status = $model::STATUS_APPROVED;
                }else $model->status = $model::STATUS_APV_MENNGETAHUI;
            }else if($isMarketing){
                if($userId != $model->sc->marketing_id){
                    throw new ForbiddenHttpException('Anda bukan Marketing yang ditunjuk, proses approval tidak bisa dilanjutkan.');
                }

                $model->apv_marketing_at = $now;
                if($model->status == $model::STATUS_APV_MENNGETAHUI){
                    $model->status = $model::STATUS_APPROVED;
                }else $model->status = $model::STATUS_APV_MARKETING;
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }

            //validasi stock greige
            $greige = $model->greige;
            $greigeGroup = $greige->group;
            $gap = 0;//(float)$greige->gap;
            $totalColorsBatch = $model->colorQty;
            $totalColorsMeter = $totalColorsBatch * ((float)$greigeGroup->qty_per_batch + $gap);

            $stockM = (float)$greige->stock;
            $bookedM = (float)$greige->booked;
            $avM = $stockM - $bookedM;

            if($avM < $totalColorsMeter){
                $stockFmt = Yii::$app->formatter->asDecimal($stockM).'M';
                $bookedFmt = Yii::$app->formatter->asDecimal($bookedM).'M';
                $avFmt = Yii::$app->formatter->asDecimal($avM).'M';
                throw new ForbiddenHttpException("Persediaan digudang greige tidak mencukupi, jumlah stock: $stockFmt, Booked: $bookedFmt, Tersedia: $avFmt");
            }

            if($model->status == $model::STATUS_APPROVED){
                $model->setNoWo();

                $greige->booked = (float)$greige->booked + $totalColorsMeter;
                $greige->save(false, ['booked']);
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