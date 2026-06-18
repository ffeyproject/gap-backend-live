<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnHambatanMesin;
use common\models\ar\TrnHambatanMesinItem;
use common\models\ar\TrnHambatanMesinSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnHambatanMesinController implements the CRUD actions for TrnHambatanMesin model.
 */
class TrnHambatanMesinController extends Controller
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
     * Lists all TrnHambatanMesin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnHambatanMesinSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnHambatanMesin model.
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
     * Creates a new TrnHambatanMesin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnHambatanMesin();
        $model->tanggal = date('Y-m-d'); // Default to today's date

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            if ($model->load($post)) {
                $itemsData = $post['Items'] ?? [];
                
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        foreach ($itemsData as $itemData) {
                            // Skip completely empty rows
                            if (empty($itemData['start_time']) && empty($itemData['stop_time']) && empty($itemData['jenis_hambatan_ids']) && empty($itemData['keterangan']) && empty($itemData['mst_mesin_proses_id'])) {
                                continue;
                            }

                            $item = new TrnHambatanMesinItem();
                            $item->trn_hambatan_mesin_id = $model->id;
                            $item->start_time = $itemData['start_time'] ?? '';
                            $item->stop_time = $itemData['stop_time'] ?? '';
                            $item->mst_mesin_proses_id = $itemData['mst_mesin_proses_id'] ?? null;
                            $item->no_kartu = $itemData['no_kartu'] ?? null;
                            $item->no_wo = $itemData['no_wo'] ?? null;
                            $item->keterangan = $itemData['keterangan'] ?? null;
                            
                            if (!$item->save()) {
                                throw new \Exception('Gagal menyimpan item hambatan: ' . implode(', ', $item->getFirstErrors()));
                            }

                            $hambatanIds = $itemData['jenis_hambatan_ids'] ?? [];
                            if (!is_array($hambatanIds)) {
                                $hambatanIds = [$hambatanIds];
                            }
                            foreach ($hambatanIds as $hambatanId) {
                                if ($hambatanId) {
                                    Yii::$app->db->createCommand()->insert('trn_hambatan_mesin_item_hambatan', [
                                        'trn_hambatan_mesin_item_id' => $item->id,
                                        'mst_jenis_hambatan_id' => $hambatanId
                                    ])->execute();
                                }
                            }
                        }
                        
                        // Validasi minimal 1 hambatan
                        $savedItemsCount = TrnHambatanMesinItem::find()->where(['trn_hambatan_mesin_id' => $model->id])->count();
                        if ($savedItemsCount == 0) {
                            throw new \Exception('Minimal harus ada satu hambatan yang diisi.');
                        }
                        
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Data hambatan per mesin berhasil disimpan.');
                        return $this->redirect(['create']);
                    } else {
                        throw new \Exception('Gagal menyimpan data hambatan.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        $queryItems = \common\models\ar\TrnHambatanMesinItem::find()
            ->joinWith('trnHambatanMesin')
            ->where(['trn_hambatan_mesin.tanggal' => date('Y-m-d')])
            ->orderBy(['trn_hambatan_mesin_item.id' => SORT_DESC]);
            
        $dataProviderItems = new \yii\data\ActiveDataProvider([
            'query' => $queryItems,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('create', [
            'model' => $model,
            'items' => [new TrnHambatanMesinItem()],
            'dataProviderItems' => $dataProviderItems,
        ]);
    }

    /**
     * Updates an existing TrnHambatanMesin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $items = $model->trnHambatanMesinItems;
        if (empty($items)) {
            $items = [new TrnHambatanMesinItem()];
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            if ($model->load($post)) {
                $itemsData = $post['Items'] ?? [];
                
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        // Delete existing items
                        $existingItems = TrnHambatanMesinItem::find()->where(['trn_hambatan_mesin_id' => $model->id])->all();
                        foreach ($existingItems as $existingItem) {
                            Yii::$app->db->createCommand()->delete('trn_hambatan_mesin_item_hambatan', [
                                'trn_hambatan_mesin_item_id' => $existingItem->id
                            ])->execute();
                            $existingItem->delete();
                        }
                        
                        foreach ($itemsData as $itemData) {
                            // Skip completely empty rows
                            if (empty($itemData['start_time']) && empty($itemData['stop_time']) && empty($itemData['jenis_hambatan_ids']) && empty($itemData['keterangan']) && empty($itemData['mst_mesin_proses_id'])) {
                                continue;
                            }

                            $item = new TrnHambatanMesinItem();
                            $item->trn_hambatan_mesin_id = $model->id;
                            $item->start_time = $itemData['start_time'] ?? '';
                            $item->stop_time = $itemData['stop_time'] ?? '';
                            $item->mst_mesin_proses_id = $itemData['mst_mesin_proses_id'] ?? null;
                            $item->no_kartu = $itemData['no_kartu'] ?? null;
                            $item->no_wo = $itemData['no_wo'] ?? null;
                            $item->keterangan = $itemData['keterangan'] ?? null;
                            
                            if (!$item->save()) {
                                throw new \Exception('Gagal menyimpan item hambatan: ' . implode(', ', $item->getFirstErrors()));
                            }

                            $hambatanIds = $itemData['jenis_hambatan_ids'] ?? [];
                            if (!is_array($hambatanIds)) {
                                $hambatanIds = [$hambatanIds];
                            }
                            foreach ($hambatanIds as $hambatanId) {
                                if ($hambatanId) {
                                    Yii::$app->db->createCommand()->insert('trn_hambatan_mesin_item_hambatan', [
                                        'trn_hambatan_mesin_item_id' => $item->id,
                                        'mst_jenis_hambatan_id' => $hambatanId
                                    ])->execute();
                                }
                            }
                        }
                        
                        // Validasi minimal 1 hambatan
                        $savedItemsCount = TrnHambatanMesinItem::find()->where(['trn_hambatan_mesin_id' => $model->id])->count();
                        if ($savedItemsCount == 0) {
                            throw new \Exception('Minimal harus ada satu hambatan yang diisi.');
                        }
                        
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Data hambatan per mesin berhasil diperbarui.');
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        throw new \Exception('Gagal memperbarui data hambatan.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'items' => $items,
        ]);
    }

    /**
     * Deletes an existing TrnHambatanMesin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Data hambatan per mesin berhasil dihapus.');

        return $this->redirect(['index']);
    }

    /**
     * Returns machines filtered by model name
     */
    public function actionGetMachinesByModel()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model_mesin = Yii::$app->request->get('model_mesin');
        $query = \common\models\ar\MstMesinProses::find();
        
        if (!empty($model_mesin)) {
            if (!is_array($model_mesin)) {
                $model_mesin = [$model_mesin];
            }
            
            $orConditions = ['or'];
            foreach ($model_mesin as $sm) {
                if ($sm === '_empty_') {
                    $orConditions[] = ['or', ['model_mesin' => null], ['model_mesin' => '']];
                } else {
                    $orConditions[] = ['model_mesin' => $sm];
                }
            }
            $query->andWhere($orConditions);
        }
        
        return $query->asArray()->all();
    }

    /**
     * Returns hambatans associated with selected machine
     */
    public function actionGetHambatansByMachine($machine_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $machine = \common\models\ar\MstMesinProses::findOne($machine_id);
        if ($machine) {
            $hambatans = $machine->mstJenisHambatans;
            return \yii\helpers\ArrayHelper::toArray($hambatans, [
                \common\models\ar\MstJenisHambatan::class => ['id', 'nama']
            ]);
        }
        return [];
    }

    /**
     * Dynamic card search
     */
    public function actionSearchKartuProses($q = null, $id = null, $wo = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q) || !is_null($wo)) {
            $results = [];
            
            // Search in Dyeing
            $queryDyeing = \common\models\ar\TrnKartuProsesDyeing::find()
                ->select(['trn_kartu_proses_dyeing.no'])
                ->andFilterWhere(['like', 'trn_kartu_proses_dyeing.no', $q]);
            if ($wo) {
                $queryDyeing->joinWith('wo', false)->andWhere(['trn_wo.no' => $wo]);
            }
            $dyeing = $queryDyeing->orderBy(['trn_kartu_proses_dyeing.id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($dyeing as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no'] . ' (Dyeing)'];
                }
            }

            // Search in Printing
            $queryPrinting = \common\models\ar\TrnKartuProsesPrinting::find()
                ->select(['trn_kartu_proses_printing.no'])
                ->andFilterWhere(['like', 'trn_kartu_proses_printing.no', $q]);
            if ($wo) {
                $queryPrinting->joinWith('wo', false)->andWhere(['trn_wo.no' => $wo]);
            }
            $printing = $queryPrinting->orderBy(['trn_kartu_proses_printing.id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($printing as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no'] . ' (Printing)'];
                }
            }

            // Search in Pfp
            $queryPfp = \common\models\ar\TrnKartuProsesPfp::find()
                ->select(['trn_kartu_proses_pfp.no'])
                ->andFilterWhere(['like', 'trn_kartu_proses_pfp.no', $q]);
            if ($wo) {
                $queryPfp->joinWith('orderPfp', false)->andWhere(['trn_order_pfp.no' => $wo]);
            }
            $pfp = $queryPfp->orderBy(['trn_kartu_proses_pfp.id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($pfp as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no'] . ' (PFP)'];
                }
            }

            // Search in Celup
            if (!$wo) {
                $queryCelup = \common\models\ar\TrnKartuProsesCelup::find()
                    ->select(['trn_kartu_proses_celup.no'])
                    ->andFilterWhere(['like', 'trn_kartu_proses_celup.no', $q]);
                $celup = $queryCelup->orderBy(['trn_kartu_proses_celup.id' => SORT_DESC])
                    ->limit(10)
                    ->asArray()
                    ->all();
                foreach ($celup as $row) {
                    if ($row['no']) {
                        $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no'] . ' (Celup)'];
                    }
                }
            }

            // Search in Maklon
            $queryMaklon = \common\models\ar\TrnKartuProsesMaklon::find()
                ->select(['trn_kartu_proses_maklon.no'])
                ->andFilterWhere(['like', 'trn_kartu_proses_maklon.no', $q]);
            if ($wo) {
                $queryMaklon->joinWith('wo', false)->andWhere(['trn_wo.no' => $wo]);
            }
            $maklon = $queryMaklon->orderBy(['trn_kartu_proses_maklon.id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($maklon as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no'] . ' (Maklon)'];
                }
            }

            $out['results'] = array_values($results);
        } elseif ($id) {
            $out['results'] = [['id' => $id, 'text' => $id]];
        }
        return $out;
    }

    /**
     * Dynamic WO search
     */
    public function actionSearchWo($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = [];
            
            $wo = \common\models\ar\TrnWo::find()
                ->select(['no'])
                ->andFilterWhere(['like', 'no', $q])
                ->orderBy(['id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($wo as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no']];
                }
            }

            $out['results'] = array_values($results);
        } elseif ($id) {
            $out['results'] = [['id' => $id, 'text' => $id]];
        }
        return $out;
    }

    /**
     * Dynamic Order PFP search
     */
    public function actionSearchOrderPfp($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = [];
            
            $orders = \common\models\ar\TrnOrderPfp::find()
                ->select(['no'])
                ->andFilterWhere(['like', 'no', $q])
                ->orderBy(['id' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            foreach ($orders as $row) {
                if ($row['no']) {
                    $results[$row['no']] = ['id' => $row['no'], 'text' => $row['no']];
                }
            }

            $out['results'] = array_values($results);
        } elseif ($id) {
            $out['results'] = [['id' => $id, 'text' => $id]];
        }
        return $out;
    }

    /**
     * Finds the TrnHambatanMesin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnHambatanMesin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnHambatanMesin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
