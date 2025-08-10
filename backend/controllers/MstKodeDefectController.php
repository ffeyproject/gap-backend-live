<?php

namespace backend\controllers;

use common\models\ar\InspectingItem;
use Yii;
use common\models\ar\MstKodeDefect;
use common\models\ar\MstKodeDefectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\Json; // Untuk penggunaan Json::encode


/**
 * MstKodeDefectController implements the CRUD actions for MstKodeDefect model.
 */
class MstKodeDefectController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MstKodeDefect models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstKodeDefectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a graph related to MstKodeDefect data.
     * @return mixed
     */
  public function actionGrafik()
{
    $currentYear = date('Y'); // Tahun saat ini

    // Debug untuk memastikan tahun yang digunakan
    \Yii::info("Current Year: " . $currentYear, __METHOD__);

    $defectPerMonth = InspectingItem::find()
    ->joinWith([
        'defectInspectingItems',
        'defectInspectingItems.mstKodeDefect',
        'inspecting'
    ])
    ->where(['EXTRACT(YEAR FROM trn_inspecting.date)' => $currentYear])
    ->select([
        'EXTRACT(MONTH FROM trn_inspecting.date) AS month',
        'COALESCE(mst_kode_defect.nama_defect, \'Unknown\') AS nama_defect', // Nama defect
        'mst_kode_defect.no_urut AS no_urut', // Tambahkan no_urut
        'COUNT(*) AS count'
    ])
    ->groupBy([
        'EXTRACT(MONTH FROM trn_inspecting.date)', 
        'mst_kode_defect.nama_defect', 
        'mst_kode_defect.no_urut'
    ]) // Group by no_urut
    ->orderBy([
        'month' => SORT_ASC, 
        'mst_kode_defect.no_urut' => SORT_ASC
    ]) // Urut berdasarkan no_urut
    ->asArray()
    ->all();


    // Debug untuk memeriksa data defect per bulan
    \Yii::info("Defect per Month: " . Json::encode($defectPerMonth), __METHOD__);

    return $this->render('grafik', [
        'defectPerMonth' => $defectPerMonth,
    ]);
}


    

    /**
     * Displays a single MstKodeDefect model.
     * @param integer $id
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
     * Creates a new MstKodeDefect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
{
    $model = new MstKodeDefect();

    // Cari no_urut terakhir
    $lastNoUrut = MstKodeDefect::find()
        ->select(['no_urut'])
        ->orderBy(['no_urut' => SORT_DESC])
        ->limit(1)
        ->one();

    $nextNoUrut = $lastNoUrut ? $lastNoUrut->no_urut + 1 : 1;
    $model->no_urut = $nextNoUrut;

    if ($model->load(Yii::$app->request->post())) {
        // Buat kode otomatis: AsalDefect + NoUrut
        $model->kode = $model->asal_defect . $model->no_urut;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil disimpan.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    /**
     * Updates an existing MstKodeDefect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Proses pembaruan model
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MstKodeDefect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MstKodeDefect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstKodeDefect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstKodeDefect::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}