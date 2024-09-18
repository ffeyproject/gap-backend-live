<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstSubLocation;
use common\models\ar\MstSubLocationSearch;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstSubLocationController implements the CRUD actions for MstSubLocation model.
 */
class MstSubLocationController extends Controller
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
     * Lists all MstLocation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstSubLocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // bagussona
    public function actionQr($id)
    {
        $model = $this->findModel($id);
        $create_qr = $model['locs_code'];

        $content = $this->renderPartial('qr', ['model' => $model]);
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [100,50], //THERMAL 100mm x 50mm
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                table {
                    width: 100%;
                    font-size:10px;
                    border-spacing: 0;
                    letter-spacing: 0px;
                } 
                th, td {
                    padding: 0.1em 0em;
                    vertical-align: top;
                }
                table.bordered th, table.bordered td, td.bordered, th.bordered {
                    border: 0.1px solid black;
                    padding: 0.1em 0.1em;
                    vertical-align: middle;
                }
            ',
            'methods' => [
                'SetTitle'=>$create_qr,
            ],
            // 'options' => [
            //     'setAutoTopMargin' => 'stretch'
            // ],
            // 'marginHeader' => 0,
            // 'marginFooter' => 0,
            'marginTop' => 0,
            'marginRight' => 0,
            'marginBottom' => 0,
            'marginLeft' => 0,
            'content' => $content,
        ]);

        return $pdf->render();
    }

    /**
     * Displays a single MstLocation model.
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
     * Creates a new MstLocation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstSubLocation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->locs_code]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MstLocation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->locs_code]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MstLocation model.
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
     * Finds the MstLocation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstLocation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $data = MstSubLocation::find()->where(['locs_code'=>$id])->one();
        if ($data !== null) {
            return $data;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
