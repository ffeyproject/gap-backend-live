<?php

namespace backend\modules\rawdata\controllers;

use Yii;
use backend\modules\rawdata\models\TrnKirimBuyerItem;
use backend\modules\rawdata\models\TrnKirimBuyerItemSearch;
use backend\modules\rawdata\models\TrnKirimBuyerBal;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

class TrnKirimBuyerItemController extends Controller
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
                    'batch-update' => ['POST'],
                    'editable-edit' => ['POST'],
                    'get-bales' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['batch-update', 'quick-update', 'get-bales', 'editable-edit'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all TrnKirimBuyerItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKirimBuyerItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
        ]);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKirimBuyerItem model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrnKirimBuyerItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKirimBuyerItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKirimBuyerItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Item #{$model->id} berhasil diperbarui.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * AJAX action to get or quick-update a TrnKirimBuyerItem model.
     * @param int $id
     * @return array
     */
    public function actionQuickUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    return [
                        'success' => true,
                        'message' => "Item #{$model->id} berhasil diperbarui.",
                    ];
                }
                return [
                    'success' => false,
                    'message' => implode(' ', $model->getFirstErrors()),
                ];
            }
        }

        $woNo = ($model->stock && $model->stock->wo) ? $model->stock->wo->no : '-';
        $buyerName = ($model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige && $model->stock->wo->mo->scGreige->sc && $model->stock->wo->mo->scGreige->sc->cust) 
            ? $model->stock->wo->mo->scGreige->sc->cust->name 
            : '-';

        $headerId = $model->kirimBuyer ? $model->kirimBuyer->header_id : null;
        $kirimBuyerId = $model->kirim_buyer_id;
        $bales = $this->queryBalesForHeadersAndBuyers($headerId ? [$headerId] : [], $kirimBuyerId ? [$kirimBuyerId] : []);

        return [
            'success' => true,
            'data' => [
                'id' => $model->id,
                'header_id' => $headerId,
                'kirim_buyer_id' => $kirimBuyerId,
                'stock_id' => $model->stock_id,
                'wo_no' => $woNo,
                'buyer_name' => $buyerName,
                'no_lot' => $model->noLot,
                'qty' => (float)$model->qty,
                'no_bal' => $model->no_bal,
                'bal_id' => $model->bal_id,
                'note' => $model->note,
                'bales' => $bales,
            ],
        ];
    }

    /**
     * AJAX action to get bal suggestions based on header_id, kirim_buyer_id, or selected item_ids.
     * @return array
     */
    public function actionGetBales()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $headerId = $request->get('header_id');
        $kirimBuyerId = $request->get('kirim_buyer_id');
        $itemIds = $request->post('item_ids', $request->get('item_ids', []));

        $headerIds = [];
        $kirimBuyerIds = [];

        if (!empty($itemIds)) {
            if (!is_array($itemIds)) {
                $itemIds = explode(',', $itemIds);
            }
            $items = TrnKirimBuyerItem::find()
                ->joinWith('kirimBuyer')
                ->where(['trn_kirim_buyer_item.id' => $itemIds])
                ->all();

            foreach ($items as $item) {
                if ($item->kirimBuyer) {
                    if ($item->kirimBuyer->header_id) {
                        $headerIds[] = (int)$item->kirimBuyer->header_id;
                    }
                    if ($item->kirimBuyer->id) {
                        $kirimBuyerIds[] = (int)$item->kirimBuyer->id;
                    }
                }
            }
        }

        if ($headerId) {
            $headerIds[] = (int)$headerId;
        }
        if ($kirimBuyerId) {
            $kirimBuyerIds[] = (int)$kirimBuyerId;
        }

        $headerIds = array_values(array_unique(array_filter($headerIds)));
        $kirimBuyerIds = array_values(array_unique(array_filter($kirimBuyerIds)));
        $bales = $this->queryBalesForHeadersAndBuyers($headerIds, $kirimBuyerIds);

        return [
            'success' => true,
            'header_ids' => $headerIds,
            'kirim_buyer_ids' => $kirimBuyerIds,
            'bales' => $bales,
        ];
    }

    /**
     * Helper to query unique bales associated with given headerIds and/or kirimBuyerIds.
     * @param array $headerIds
     * @param array $kirimBuyerIds
     * @return array
     */
    protected function queryBalesForHeadersAndBuyers(array $headerIds, array $kirimBuyerIds)
    {
        $headerIds = array_values(array_unique(array_filter($headerIds)));
        $kirimBuyerIds = array_values(array_unique(array_filter($kirimBuyerIds)));

        if (empty($headerIds) && empty($kirimBuyerIds)) {
            return [];
        }

        $bales = [];
        $seen = [];

        // 1. Query from TrnKirimBuyerBal
        $conditions = ['or'];
        if (!empty($headerIds)) {
            $conditions[] = ['header_id' => $headerIds];
        }
        if (!empty($kirimBuyerIds)) {
            $conditions[] = ['trn_kirim_buyer_id' => $kirimBuyerIds];
        }

        if (count($conditions) > 1) {
            $balModels = TrnKirimBuyerBal::find()
                ->where($conditions)
                ->orderBy(['no_bal' => SORT_ASC, 'id' => SORT_ASC])
                ->asArray()
                ->all();

            foreach ($balModels as $b) {
                $noBal = trim((string)$b['no_bal']);
                $balId = (int)$b['id'];
                $key = $noBal . '_' . $balId;
                if (!isset($seen[$key]) && $noBal !== '') {
                    $seen[$key] = true;
                    $bales[] = [
                        'id' => $balId,
                        'no_bal' => $noBal,
                        'header_id' => $b['header_id'],
                        'kirim_buyer_id' => $b['trn_kirim_buyer_id'],
                        'label' => "No. Bal: {$noBal} (Bal ID: {$balId})",
                    ];
                }
            }
        }

        // 2. Query from TrnKirimBuyerItem for the same headers/kirim_buyer
        $itemQuery = (new \yii\db\Query())
            ->from('trn_kirim_buyer_item i')
            ->innerJoin('trn_kirim_buyer kb', 'kb.id = i.kirim_buyer_id')
            ->select(['i.no_bal', 'i.bal_id', 'kb.header_id', 'i.kirim_buyer_id'])
            ->where(['or', ['in', 'kb.header_id', $headerIds], ['in', 'i.kirim_buyer_id', $kirimBuyerIds]])
            ->andWhere(['is not', 'i.no_bal', null])
            ->andWhere(['!=', 'i.no_bal', ''])
            ->distinct();

        foreach ($itemQuery->all() as $row) {
            $noBal = trim((string)$row['no_bal']);
            $balId = $row['bal_id'] ? (int)$row['bal_id'] : null;
            $key = $noBal . '_' . ($balId ?: '0');
            if (!isset($seen[$key]) && $noBal !== '') {
                $seen[$key] = true;
                $bales[] = [
                    'id' => $balId,
                    'no_bal' => $noBal,
                    'header_id' => $row['header_id'],
                    'kirim_buyer_id' => $row['kirim_buyer_id'],
                    'label' => "No. Bal: {$noBal}" . ($balId ? " (Bal ID: {$balId})" : ""),
                ];
            }
        }

        // Sort naturally by no_bal
        usort($bales, function($a, $b) {
            return strnatcasecmp($a['no_bal'], $b['no_bal']);
        });

        return $bales;
    }

    /**
     * AJAX action to batch update selected TrnKirimBuyerItem models.
     * @return array
     */
    public function actionBatchUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $ids = $request->post('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return [
                'success' => false,
                'message' => 'Pilih minimal satu item untuk diperbarui.',
            ];
        }

        $applyNoBal = (int)$request->post('apply_no_bal', 0);
        $setNoBal = $request->post('set_no_bal', '');

        $applyBalId = (int)$request->post('apply_bal_id', 0);
        $setBalId = $request->post('set_bal_id', '');

        $applyNote = (int)$request->post('apply_note', 0);
        $setNote = $request->post('set_note', '');
        $noteMode = $request->post('note_mode', 'replace'); // 'replace' or 'append'

        if (!$applyNoBal && !$applyBalId && !$applyNote) {
            return [
                'success' => false,
                'message' => 'Centang minimal satu kolom (No. Bal, Bal ID, atau Catatan) yang ingin diubah.',
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();
        $updatedCount = 0;

        try {
            $models = TrnKirimBuyerItem::findAll(['id' => $ids]);
            foreach ($models as $item) {
                $changed = false;

                if ($applyNoBal) {
                    $item->no_bal = $setNoBal !== '' ? trim($setNoBal) : null;
                    $changed = true;
                }

                if ($applyBalId) {
                    $item->bal_id = $setBalId !== '' ? (int)$setBalId : null;
                    $changed = true;
                }

                if ($applyNote) {
                    $trimmedNote = trim($setNote);
                    if ($noteMode === 'append') {
                        $item->note = !empty($item->note) 
                            ? ($item->note . "\n" . $trimmedNote) 
                            : $trimmedNote;
                    } else {
                        $item->note = $trimmedNote !== '' ? $trimmedNote : null;
                    }
                    $changed = true;
                }

                if ($changed) {
                    if (!$item->save(false)) {
                        throw new \Exception("Gagal menyimpan item ID #{$item->id}");
                    }
                    $updatedCount++;
                }
            }

            $transaction->commit();

            return [
                'success' => true,
                'message' => "Berhasil memperbarui {$updatedCount} item secara massal.",
                'updatedCount' => $updatedCount,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat batch update: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * AJAX action to handle inline cell update.
     * @return array
     */
    public function actionEditableEdit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $id = $request->post('id') ?: $request->post('editableKey');
        $attribute = $request->post('attribute') ?: $request->post('editableAttribute');

        if (!$id) {
            return ['success' => false, 'output' => '', 'message' => 'ID item tidak ditemukan.'];
        }

        try {
            $model = $this->findModel($id);
        } catch (\Exception $e) {
            return ['success' => false, 'output' => '', 'message' => 'Item ID #' . $id . ' tidak ditemukan: ' . $e->getMessage()];
        }

        // Handle Kartik editable post
        if ($request->post('hasEditable')) {
            $posted = current($_POST['TrnKirimBuyerItem']);
            $post = ['TrnKirimBuyerItem' => $posted];

            if ($model->load($post)) {
                if ($model->save()) {
                    $output = $attribute ? $model->$attribute : '';
                    return ['success' => true, 'output' => $output, 'value' => $output, 'message' => ''];
                } else {
                    $errorMsg = implode(', ', $model->getFirstErrors());
                    return ['success' => false, 'output' => '', 'message' => $errorMsg];
                }
            }
        }

        // Handle direct inline cell edit
        if ($attribute && in_array($attribute, ['no_bal', 'note', 'bal_id', 'qty'])) {
            $val = $request->post('value');
            $model->$attribute = ($val !== '' && $val !== null) ? trim($val) : null;

            // Jika ubah no_bal, sinkronkan bal_id jika nomor bal tersebut sudah terdaftar di header / buyer
            if ($attribute === 'no_bal') {
                if (!empty($model->no_bal)) {
                    $headerId = $model->kirimBuyer ? $model->kirimBuyer->header_id : null;
                    $conditions = ['or'];
                    if ($headerId) {
                        $conditions[] = ['header_id' => $headerId];
                    }
                    if ($model->kirim_buyer_id) {
                        $conditions[] = ['trn_kirim_buyer_id' => $model->kirim_buyer_id];
                    }
                    if (count($conditions) > 1) {
                        $bal = TrnKirimBuyerBal::find()
                            ->where(['no_bal' => $model->no_bal])
                            ->andWhere($conditions)
                            ->one();
                        if ($bal) {
                            $model->bal_id = $bal->id;
                        }
                    }
                } else {
                    $model->bal_id = null;
                }
            }

            if ($model->save(false)) {
                return [
                    'success' => true,
                    'value' => (string)$model->$attribute,
                    'bal_id' => $model->bal_id,
                    'message' => 'Data berhasil disimpan.',
                ];
            } else {
                $errors = $model->getFirstErrors();
                return [
                    'success' => false,
                    'message' => !empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan data.',
                ];
            }
        }

        return ['success' => false, 'output' => '', 'message' => 'Permintaan tidak valid.'];
    }

    /**
     * Deletes an existing TrnKirimBuyerItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Ubah status pada TrnGudangJadi jika ada stock_id
        if ($model->stock_id) {
            $stock = \common\models\ar\TrnGudangJadi::findOne($model->stock_id);
            if ($stock !== null) {
                $stock->status = 1; // atau sesuai nilai status yang diinginkan
                $stock->save(false); // skip validation jika tidak perlu
            }
        }

        // Hapus item kirim
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrnKirimBuyerItem model based on its primary key value.
     * @param int $id
     * @return TrnKirimBuyerItem
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = TrnKirimBuyerItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested item does not exist.');
    }
}