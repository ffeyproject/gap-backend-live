<?php

namespace backend\controllers;

use common\models\ar\TrnKirimBuyerHeader;
use common\models\ar\TrnMo;
use common\models\ar\TrnMoColorSearch;
use common\models\ar\TrnMoSearch;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreigeSearch;
use common\models\ar\TrnScSearch;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColorSearch;
use common\models\ar\TrnWoSearch;
use Yii;
use yii\web\Controller;

/**
 *
*/
class RekapController extends Controller
{
    /**
     * Lists all TrnSc models.
     * @return mixed
     */
    public function actionSc()
    {
        $searchModel = new TrnScGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_sc.status'=>TrnSc::STATUS_APPROVED]);

        return $this->render('sc', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnSc models.
     * @return mixed
     */
    public function actionMo()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan statistik MO yang statusnya sudah disetujui.');
        $searchModel = new TrnMoSearch(['status'=>TrnMo::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('mo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnMoColor models.
     * @return mixed
     */
    public function actionMoColor()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan statistik MO Color yang status MO nya sudah disetujui.');
        $searchModel = new TrnMoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_mo.status'=>TrnMo::STATUS_APPROVED]);

        return $this->render('mo-color', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionWo()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan statistik WO yang status nya sudah disetujui.');
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED]);

        return $this->render('wo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionOutstandingPmc()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan data outstanding PMC.');
        $searchModel = new TrnWoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED]);

        return $this->render('outstanding-pmc', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionAccounting()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan statistik WO untuk kebutuhan bagian keuangan.');

        $searchModel = new \backend\models\TrnKirimBuyerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_kirim_buyer_header.status'=>TrnKirimBuyerHeader::STATUS_POSTED]);

        return $this->render('accounting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionWoColor()
    {
        Yii::$app->session->setFlash('info', 'Menampilkan statistik WO Color yang status WO nya sudah disetujui.');
        $searchModel = new TrnWoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED]);

        return $this->render('wo-color', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}