<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use backend\models\form\HasilTesGosokForm;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnWoColorSearch;
use common\models\ar\TrnScGreige;
use Yii;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingSearch;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKartuProsesDyeingController implements the CRUD actions for TrnKartuProsesDyeing model.
 */
class RealisasiDyeingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Ini adalah fungsi untuk menuju halaman rekap formated
     * halaman tersebut adalah halaman yang menampilkan data rekap dyeing sesuai fromat permintaan dari divisi dyeing
     */
    public function actionRekapFormated()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_DRAFT]);

        $dataProvider->query->andWhere(['<=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_DELIVERED]);

        $dataProvider->query->orderBy(['trn_wo.no' => SORT_DESC])
            ->addOrderBy(['mst_greige.nama_kain' => SORT_DESC])
            ->addOrderBy(['moColor.color' => SORT_DESC]);

        return $this->render('rekap-formated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionRekapFormatedNoNk()
    {
        $searchModel = new TrnWoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);

        $dataProvider->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);

        $dataProvider->query->andWhere(['not exists', (new \yii\db\Query())
            ->select('id')
            ->from('trn_kartu_proses_dyeing')
            ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
        ]);

        return $this->render('rekap-formated-no-nk', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

        /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekapOutstandingBukaanDyeing()
    {
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
        $dataProvider->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);

        return $this->render('rekap-outstanding-bukaan-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the TrnKartuProsesDyeing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesDyeing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesDyeing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
