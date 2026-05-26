<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstMesinProses;
use common\models\ar\MstMesinProsesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstMesinProsesController implements the CRUD actions for MstMesinProses model.
 */
class MstMesinProsesController extends Controller
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
     * Lists all MstMesinProses models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstMesinProsesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MstMesinProses model.
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
     * Creates a new MstMesinProses model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstMesinProses();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('MstMesinProses');
            $model_mesin = $post['model_mesin'] ?? null;
            $nama_mesin_input = $post['nama_mesin'] ?? '';
            $hambatan_post = Yii::$app->request->post('jenis_hambatan') ?? [];
            if (!is_array($hambatan_post)) {
                $hambatan_post = [];
            }

            // Parse names of machines
            $names = preg_split('/[\r\n,]+/', $nama_mesin_input);
            $names = array_filter(array_map('trim', $names));

            // Find or create hambatans
            $hambatan_ids = [];
            foreach ($hambatan_post as $item) {
                if (is_numeric($item)) {
                    $hambatanModel = \common\models\ar\MstJenisHambatan::findOne($item);
                    if ($hambatanModel) {
                        $hambatan_ids[] = $hambatanModel->id;
                        continue;
                    }
                }
                
                $hambatanModel = \common\models\ar\MstJenisHambatan::findOne(['nama' => $item]);
                if (!$hambatanModel) {
                    $hambatanModel = new \common\models\ar\MstJenisHambatan();
                    $hambatanModel->nama = $item;
                    $hambatanModel->save(false);
                }
                $hambatan_ids[] = $hambatanModel->id;
            }

            // Create machines in bulk
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (empty($names)) {
                    $model->addError('nama_mesin', 'Nama/Nomor Mesin tidak boleh kosong.');
                    throw new \Exception('Nama/Nomor Mesin kosong.');
                }

                foreach ($names as $name) {
                    $machine = new MstMesinProses();
                    $machine->nama_mesin = $name;
                    $machine->model_mesin = $model_mesin;
                    if ($machine->save()) {
                        // Link hambatans
                        foreach ($hambatan_ids as $hambatan_id) {
                            $hambatanModel = \common\models\ar\MstJenisHambatan::findOne($hambatan_id);
                            if ($hambatanModel) {
                                $machine->link('mstJenisHambatans', $hambatanModel);
                            }
                        }
                    } else {
                        $model->addErrors($machine->getErrors());
                        throw new \Exception('Gagal menyimpan mesin: ' . implode(', ', $machine->getFirstErrors()));
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', count($names) . ' Mesin berhasil disimpan.');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MstMesinProses model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('MstMesinProses');
            $model->model_mesin = $post['model_mesin'] ?? null;
            $model->nama_mesin = $post['nama_mesin'] ?? '';
            $hambatan_post = Yii::$app->request->post('jenis_hambatan') ?? [];
            if (!is_array($hambatan_post)) {
                $hambatan_post = [];
            }

            // Find or create hambatans
            $hambatan_ids = [];
            foreach ($hambatan_post as $item) {
                if (is_numeric($item)) {
                    $hambatanModel = \common\models\ar\MstJenisHambatan::findOne($item);
                    if ($hambatanModel) {
                        $hambatan_ids[] = $hambatanModel->id;
                        continue;
                    }
                }
                
                $hambatanModel = \common\models\ar\MstJenisHambatan::findOne(['nama' => $item]);
                if (!$hambatanModel) {
                    $hambatanModel = new \common\models\ar\MstJenisHambatan();
                    $hambatanModel->nama = $item;
                    $hambatanModel->save(false);
                }
                $hambatan_ids[] = $hambatanModel->id;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Unlink all current hambatans
                    $model->unlinkAll('mstJenisHambatans', true);

                    // Link new hambatans
                    foreach ($hambatan_ids as $hambatan_id) {
                        $hambatanModel = \common\models\ar\MstJenisHambatan::findOne($hambatan_id);
                        if ($hambatanModel) {
                            $model->link('mstJenisHambatans', $hambatanModel);
                        }
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Data berhasil diubah.');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception('Gagal mengubah data mesin.');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal mengubah data: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MstMesinProses model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the MstMesinProses model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstMesinProses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstMesinProses::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
