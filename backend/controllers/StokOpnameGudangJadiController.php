<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\WmsMoveLocationMstr;
use common\models\ar\WmsMoveLocationDtl;
use common\models\ar\MstSubLocation;
use backend\models\TrnGudangJadiOpnamePcsSearch;
use backend\models\StokOpnameGudangJadiRekapSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StokOpnameGudangJadiController implements CRUD & Rekap actions for TrnGudangJadiOpnamePcs.
 */
class StokOpnameGudangJadiController extends Controller
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
                    'delete-batch' => ['POST'],
                    'save-location' => ['POST'],
                    'move-location' => ['POST'],
                    'sync-color' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnGudangJadiOpnamePcs models (Data Pcs).
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiOpnamePcsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $queryCount = clone $dataProvider->query;
        $totalPcs = $queryCount->count();

        $queryQty = clone $dataProvider->query;
        $totalQty = $queryQty->sum('t.qty') ?: 0;

        $queryRak = clone $dataProvider->query;
        $listRak = $queryRak->select('t.locs_code')
            ->distinct()
            ->andWhere(['is not', 't.locs_code', null])
            ->andWhere(['!=', 't.locs_code', ''])
            ->orderBy(['t.locs_code' => SORT_ASC])
            ->column();

        $totalRak = count($listRak);

        $queryStock = clone $dataProvider->query;
        $totalStock = $queryStock->andWhere(['t.status' => TrnGudangJadiOpnamePcs::STATUS_STOCK])->count();
        $queryQtyStock = clone $dataProvider->query;
        $totalQtyStock = $queryQtyStock->andWhere(['t.status' => TrnGudangJadiOpnamePcs::STATUS_STOCK])->sum('t.qty') ?: 0;

        $queryOut = clone $dataProvider->query;
        $totalOut = $queryOut->andWhere(['t.status' => TrnGudangJadiOpnamePcs::STATUS_OUT])->count();
        $queryQtyOut = clone $dataProvider->query;
        $totalQtyOut = $queryQtyOut->andWhere(['t.status' => TrnGudangJadiOpnamePcs::STATUS_OUT])->sum('t.qty') ?: 0;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPcs' => $totalPcs,
            'totalQty' => $totalQty,
            'totalRak' => $totalRak,
            'listRak' => $listRak,
            'totalStock' => $totalStock,
            'totalQtyStock' => $totalQtyStock,
            'totalOut' => $totalOut,
            'totalQtyOut' => $totalQtyOut,
        ]);
    }

    /**
     * Sinkronisasi status OUT: Hanya memeriksa data opname yang statusnya masih Stock (belum OUT),
     * lalu mengecek status fisik master Gudang Jadi-nya. Jika di Gudang Jadi sudah bukan Stock (misal Out/Surat Jalan/Mutasi),
     * maka status opname diubah menjadi OUT. Data opname yang sudah OUT diabaikan dan tidak dibaca lagi.
     * @return mixed
     */
    public function actionSyncStatusOut()
    {
        // 1. Ambil id_trn_gudang_jadi dari data opname yang statusnya masih Stock / belum OUT
        $activeOpnameGudangJadiIds = (new \yii\db\Query())
            ->select('id_trn_gudang_jadi')
            ->from('trn_gudang_jadi_opname_pcs')
            ->where(['!=', 'status', TrnGudangJadiOpnamePcs::STATUS_OUT])
            ->andWhere(['is not', 'id_trn_gudang_jadi', null]);

        // 2. Cari di master trn_gudang_jadi mana saja yang status fisiknya sudah BUKAN STATUS_STOCK
        $outGudangJadiIds = (new \yii\db\Query())
            ->select('id')
            ->from('trn_gudang_jadi')
            ->where(['in', 'id', $activeOpnameGudangJadiIds])
            ->andWhere(['!=', 'status', \common\models\ar\TrnGudangJadi::STATUS_STOCK]);

        // 3. Update status data opname tersebut menjadi STATUS_OUT
        $updatedOut = TrnGudangJadiOpnamePcs::updateAll(
            [
                'status' => TrnGudangJadiOpnamePcs::STATUS_OUT,
                'updated_at' => time(),
                'updated_by' => Yii::$app->user->id,
            ],
            [
                'and',
                ['in', 'id_trn_gudang_jadi', $outGudangJadiIds],
                ['!=', 'status', TrnGudangJadiOpnamePcs::STATUS_OUT],
            ]
        );

        Yii::$app->session->setFlash('success', "Sinkronisasi selesai: {$updatedOut} item opname berstatus Stock berhasil diubah menjadi OUT.");
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Sinkronisasi status Stock dan Lokasi ke Gudang Jadi:
     * Mengubah status master Gudang Jadi (trn_gudang_jadi) yang berelasi menjadi STATUS_STOCK
     * dan menyinkronkan lokasi (locs_code) dari seluruh data Stok Opname yang berstatus Stock atau Verified (belum OUT).
     * @return mixed
     */
    public function actionSyncStatusStockGudangJadi()
    {
        $db = Yii::$app->db;
        $userId = Yii::$app->user->id;
        $now = time();

        $sql = "
            UPDATE trn_gudang_jadi gj
            SET 
                status = :status_stock,
                locs_code = COALESCE(NULLIF(op.locs_code, ''), gj.locs_code),
                qr_code = COALESCE(NULLIF(gj.qr_code, ''), NULLIF(op.qr_code, '')),
                qr_code_desc = COALESCE(NULLIF(gj.qr_code_desc, ''), NULLIF(op.qr_code_desc, '')),
                updated_at = :updated_at,
                updated_by = :updated_by
            FROM trn_gudang_jadi_opname_pcs op
            WHERE op.id_trn_gudang_jadi = gj.id
              AND op.status != :status_out
              AND op.id_trn_gudang_jadi IS NOT NULL
              AND (
                  gj.status != :status_stock 
                  OR (op.locs_code IS NOT NULL AND op.locs_code != '' AND (gj.locs_code IS NULL OR gj.locs_code != op.locs_code))
                  OR (op.qr_code IS NOT NULL AND op.qr_code != '' AND (gj.qr_code IS NULL OR gj.qr_code = ''))
              )
        ";

        $updatedStock = $db->createCommand($sql, [
            ':status_stock' => TrnGudangJadi::STATUS_STOCK,
            ':status_out' => TrnGudangJadiOpnamePcs::STATUS_OUT,
            ':updated_at' => $now,
            ':updated_by' => $userId,
        ])->execute();

        Yii::$app->session->setFlash('success', "Sinkronisasi selesai: {$updatedStock} item di Gudang Jadi berhasil diperbarui status Stock, Lokasi, dan QR Code.");
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Sinkronisasi dan Tambah Stock Gudang Jadi secara massal untuk semua data Stok Opname yang ID Gudang Jadi-nya masih kosong.
     * @return mixed
     */
    public function actionSyncStockGudangJadi()
    {
        $opnameList = TrnGudangJadiOpnamePcs::find()
            ->where(['id_trn_gudang_jadi' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        if (empty($opnameList)) {
            Yii::$app->session->setFlash('info', 'Semua data Stok Opname sudah memiliki relasi ke Stock Gudang Jadi.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        $linkedCount = 0;
        $createdCount = 0;
        $failedCount = 0;
        $failedMessages = [];
        $userId = Yii::$app->user->id;

        foreach ($opnameList as $opname) {
            $res = $opname->syncGudangJadiStock($userId);
            if ($res['success']) {
                if ($res['action'] === 'linked') {
                    $linkedCount++;
                } else {
                    $createdCount++;
                }
            } else {
                $failedCount++;
                if (count($failedMessages) < 5) {
                    $failedMessages[] = "#{$opname->id} ({$opname->qr_code}): {$res['message']}";
                }
            }
        }

        $msg = "Sinkronisasi Stock Gudang Jadi selesai: {$linkedCount} data berhasil dihubungkan ke stock Gudang Jadi yang sudah ada.";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} data tidak ditemukan di master Gudang Jadi dan dilewati).";
            Yii::$app->session->setFlash('warning', $msg);
        } else {
            Yii::$app->session->setFlash('success', $msg);
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Buat dan sinkronkan Stock Gudang Jadi untuk single item Stok Opname.
     * @param int $id
     * @return mixed
     */
    public function actionCreateStock($id)
    {
        $model = $this->findModel($id);
        $res = $model->syncGudangJadiStock(Yii::$app->user->id);

        if ($res['success']) {
            Yii::$app->session->setFlash('success', $res['message']);
        } else {
            Yii::$app->session->setFlash('error', $res['message']);
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['view', 'id' => $model->id]);
    }

    /**
     * Menghapus banyak data Stok Opname terpilih yang statusnya masih Stock.
     * @return array
     */
    public function actionDeleteBatch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ids = Yii::$app->request->post('ids');
        if (empty($ids) || !is_array($ids)) {
            return ['success' => false, 'message' => 'Pilih data yang akan dihapus terlebih dahulu.'];
        }

        // Ambil data yang berstatus STATUS_STOCK
        $modelsToDelete = TrnGudangJadiOpnamePcs::find()
            ->where(['id' => $ids])
            ->andWhere(['status' => TrnGudangJadiOpnamePcs::STATUS_STOCK])
            ->all();

        if (empty($modelsToDelete)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data berstatus Stock di antara item yang dipilih. Data berstatus Verified atau Out tidak dapat dihapus.'
            ];
        }

        $deletedCount = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($modelsToDelete as $m) {
                if ($m->delete()) {
                    $deletedCount++;
                }
            }
            $transaction->commit();

            $totalSelected = count($ids);
            $skippedCount = $totalSelected - $deletedCount;
            $msg = "Berhasil menghapus {$deletedCount} data Stok Opname berstatus Stock dan mengubah lokasi stock roll terkait di Gudang Jadi menjadi Transit.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} data dilewati karena statusnya bukan Stock).";
            }

            return [
                'success' => true,
                'message' => $msg,
                'deleted_count' => $deletedCount,
                'skipped_count' => $skippedCount,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sinkronkan Warna/Color item Stok Opname terpilih ke master Gudang Jadi (trn_gudang_jadi.color).
     * @return array
     */
    public function actionSyncColor()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ids = Yii::$app->request->post('ids');
        if (empty($ids) || !is_array($ids)) {
            return ['success' => false, 'message' => 'Pilih data yang akan disinkronkan warnanya terlebih dahulu.'];
        }

        $models = TrnGudangJadiOpnamePcs::find()->where(['id' => $ids])->all();
        if (empty($models)) {
            return ['success' => false, 'message' => 'Data opname tidak ditemukan.'];
        }

        $updatedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $userId = (Yii::$app->user && !Yii::$app->user->isGuest) ? Yii::$app->user->id : 1;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($models as $m) {
                $gudangJadi = null;
                if (!empty($m->id_trn_gudang_jadi)) {
                    $gudangJadi = TrnGudangJadi::findOne($m->id_trn_gudang_jadi);
                }

                if (!$gudangJadi) {
                    $parsed = $m->getParsedQrData();
                    if (!empty($parsed['item_id']) && !empty($parsed['ins_type'])) {
                        $gudangJadi = TrnGudangJadi::findOne(['id_from' => $parsed['item_id'], 'trans_from' => $parsed['ins_type']]);
                    }
                }

                if (!$gudangJadi && !empty($m->qr_code)) {
                    $parsed = $m->getParsedQrData();
                    $cleanQr = (!empty($parsed['ins_type']) && !empty($parsed['ins_id']) && !empty($parsed['item_id']))
                        ? ($parsed['ins_type'] . '-' . $parsed['ins_id'] . '-' . $parsed['item_id'])
                        : substr($m->qr_code, 0, 25);
                    $gudangJadi = TrnGudangJadi::findOne(['qr_code' => $cleanQr]);
                }

                if ($gudangJadi !== null) {
                    // Dapatkan warna hasil resolve dari opname / QR / Inspecting / No Lot / MO
                    $resolvedColor = $m->getResolvedColor();

                    if (!empty($resolvedColor) && !TrnGudangJadiOpnamePcs::isPlaceholderColor($resolvedColor)) {
                        $gudangJadi->color = substr(trim($resolvedColor), 0, 255);
                        $gudangJadi->updated_at = time();
                        $gudangJadi->updated_by = $userId;
                        $gudangJadi->save(false, ['color', 'updated_at', 'updated_by']);

                        if (empty($m->id_trn_gudang_jadi)) {
                            $m->id_trn_gudang_jadi = $gudangJadi->id;
                            $m->save(false, ['id_trn_gudang_jadi']);
                        }

                        $updatedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    $failedCount++;
                }
            }

            $transaction->commit();

            $msg = "Sinkronisasi Warna selesai: {$updatedCount} stock roll di Gudang Jadi berhasil diperbarui warnanya.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} item dilewati karena warna belum teridentifikasi).";
            }
            if ($failedCount > 0) {
                $msg .= " ({$failedCount} item belum terhubung ke Gudang Jadi).";
            }

            return [
                'success' => true,
                'message' => $msg,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'failed_count' => $failedCount,
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyinkronkan warna: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Rekap Stok Opname Gudang Jadi (by Motif, Color, Opname Code, Location, Grade & Status).
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new StokOpnameGudangJadiRekapSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Summary cards
        $totalPcsAll = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->count();
        $totalQtyAll = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->sum('qty') ?: 0;
        $totalVerified = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_VERIFIED])->count();
        $totalQtyVerified = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_VERIFIED])->sum('qty') ?: 0;
        $totalStock = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_STOCK])->count();
        $totalQtyStock = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_STOCK])->sum('qty') ?: 0;
        $totalOut = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_OUT])->count();
        $totalQtyOut = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_OUT])->sum('qty') ?: 0;

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPcsAll' => $totalPcsAll ?: 0,
            'totalQtyAll' => $totalQtyAll ?: 0,
            'totalVerified' => $totalVerified ?: 0,
            'totalQtyVerified' => $totalQtyVerified ?: 0,
            'totalStock' => $totalStock ?: 0,
            'totalQtyStock' => $totalQtyStock ?: 0,
            'totalOut' => $totalOut ?: 0,
            'totalQtyOut' => $totalQtyOut ?: 0,
        ]);
    }

    /**
     * Displays a single TrnGudangJadiOpnamePcs model.
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
     * Deletes an existing TrnGudangJadiOpnamePcs model (hanya jika statusnya masih Stock).
     * If deletion is successful, the browser will be redirected to the previous page or 'index'.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->status !== TrnGudangJadiOpnamePcs::STATUS_STOCK) {
            Yii::$app->session->setFlash('error', 'Hanya data opname berstatus Stock yang dapat dihapus.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        $qrCode = $model->qr_code;
        $model->delete();

        Yii::$app->session->setFlash('success', "Data Stok Opname Pcs #{$id} ({$qrCode}) berhasil dihapus dan lokasi stock roll di Gudang Jadi telah diubah menjadi Transit.");

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Update location (locs_code) via AJAX
     * @return array
     */
    public function actionSaveLocation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $location = trim((string)Yii::$app->request->post('location'));

        if (empty($id) || empty($location)) {
            return ['success' => false, 'message' => 'ID Stok Opname dan Lokasi harus diisi.'];
        }

        $targetLocModel = MstSubLocation::findOne(['locs_code' => $location]);
        if (!$targetLocModel) {
            return ['success' => false, 'message' => "Lokasi '{$location}' tidak valid atau tidak terdaftar di master lokasi."];
        }

        $model = $this->findModel($id);
        $userId = (Yii::$app->user && !Yii::$app->user->isGuest) ? Yii::$app->user->id : 1;
        $now = time();

        $model->locs_code = $targetLocModel->locs_code;
        $model->updated_at = $now;
        $model->updated_by = $userId;

        // Cari atau sinkronkan data TrnGudangJadi yang sesuai
        $gudangJadi = null;
        if (!empty($model->id_trn_gudang_jadi)) {
            $gudangJadi = TrnGudangJadi::findOne($model->id_trn_gudang_jadi);
        }

        if (!$gudangJadi) {
            $parsed = $model->getParsedQrData();
            if (!empty($parsed['item_id']) && !empty($parsed['ins_type'])) {
                $gudangJadi = TrnGudangJadi::findOne(['id_from' => $parsed['item_id'], 'trans_from' => $parsed['ins_type']]);
            }
        }

        if (!$gudangJadi && !empty($model->qr_code)) {
            $parsed = $model->getParsedQrData();
            $cleanQr = (!empty($parsed['ins_type']) && !empty($parsed['ins_id']) && !empty($parsed['item_id']))
                ? ($parsed['ins_type'] . '-' . $parsed['ins_id'] . '-' . $parsed['item_id'])
                : substr($model->qr_code, 0, 25);
            $gudangJadi = TrnGudangJadi::findOne(['qr_code' => $cleanQr]);
            if (!$gudangJadi) {
                $gudangJadi = TrnGudangJadi::find()->where(['qr_code' => $model->qr_code])->one();
            }
        }

        if ($gudangJadi !== null) {
            $gudangJadi->locs_code = $targetLocModel->locs_code;
            $gudangJadi->updated_at = $now;
            $gudangJadi->updated_by = $userId;
            $gudangJadi->save(false, ['locs_code', 'updated_at', 'updated_by']);

            if (empty($model->id_trn_gudang_jadi)) {
                $model->id_trn_gudang_jadi = $gudangJadi->id;
            }
        }

        if ($model->save(false, ['locs_code', 'id_trn_gudang_jadi', 'updated_at', 'updated_by'])) {
            return ['success' => true, 'message' => 'Lokasi berhasil diperbarui dan disinkronkan ke Gudang Jadi.', 'location' => $model->locs_code];
        }

        return ['success' => false, 'message' => 'Gagal memperbarui lokasi.'];
    }

    /**
     * Move location for selected TrnGudangJadiOpnamePcs items and record to WMS Move Location
     * @return array
     */
    public function actionMoveLocation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ids = Yii::$app->request->post('ids');
        $targetLocsCode = trim((string)Yii::$app->request->post('target_locs_code'));

        if (empty($ids) || !is_array($ids) || empty($targetLocsCode)) {
            return ['success' => false, 'message' => 'Pilih data yang akan dipindahkan dan tentukan lokasi tujuan.'];
        }

        $targetLocModel = MstSubLocation::findOne(['locs_code' => $targetLocsCode]);
        if (!$targetLocModel) {
            return ['success' => false, 'message' => "Lokasi tujuan '{$targetLocsCode}' tidak valid atau tidak terdaftar di master lokasi."];
        }

        $models = TrnGudangJadiOpnamePcs::find()->where(['id' => $ids])->all();
        if (empty($models)) {
            return ['success' => false, 'message' => 'Data tidak ditemukan.'];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $moveCode = WmsMoveLocationMstr::generateMoveCode();
            $fromLocations = [];
            foreach ($models as $model) {
                $c = trim((string)$model->locs_code);
                if (!empty($c) && $c !== '-') {
                    $fromLocations[] = $c;
                }
            }
            $fromLocations = array_values(array_unique($fromLocations));
            
            // Validasi foreign key: kolom move_locs_code_from merujuk ke wms_locs_sub.locs_code
            $fromLocCode = null;
            if (count($fromLocations) === 1) {
                $checkLoc = MstSubLocation::findOne(['locs_code' => $fromLocations[0]]);
                if ($checkLoc) {
                    $fromLocCode = $checkLoc->locs_code;
                }
            }

            // Insert Master
            $moveMstr = new WmsMoveLocationMstr();
            $moveMstr->move_code = $moveCode;
            $moveMstr->move_date = date('Y-m-d');
            $moveMstr->move_create_at = date('Y-m-d H:i:s');
            $moveMstr->move_create_by = (Yii::$app->user && !Yii::$app->user->isGuest) ? Yii::$app->user->id : 1;
            $moveMstr->move_count = count($models);
            $moveMstr->move_locs_code_from = $fromLocCode;
            $moveMstr->move_locs_code_to = $targetLocModel->locs_code;

            if (!$moveMstr->save(false)) {
                throw new \Exception('Gagal menyimpan master perpindahan lokasi.');
            }

            $userId = (Yii::$app->user && !Yii::$app->user->isGuest) ? Yii::$app->user->id : 1;
            $now = time();

            foreach ($models as $m) {
                // Cari atau sinkronkan data TrnGudangJadi yang sesuai
                $gudangJadi = null;
                if (!empty($m->id_trn_gudang_jadi)) {
                    $gudangJadi = TrnGudangJadi::findOne($m->id_trn_gudang_jadi);
                }

                if (!$gudangJadi) {
                    $parsed = $m->getParsedQrData();
                    if (!empty($parsed['item_id']) && !empty($parsed['ins_type'])) {
                        $gudangJadi = TrnGudangJadi::findOne(['id_from' => $parsed['item_id'], 'trans_from' => $parsed['ins_type']]);
                    }
                }

                if (!$gudangJadi && !empty($m->qr_code)) {
                    $parsed = $m->getParsedQrData();
                    $cleanQr = (!empty($parsed['ins_type']) && !empty($parsed['ins_id']) && !empty($parsed['item_id']))
                        ? ($parsed['ins_type'] . '-' . $parsed['ins_id'] . '-' . $parsed['item_id'])
                        : substr($m->qr_code, 0, 25);
                    $gudangJadi = TrnGudangJadi::findOne(['qr_code' => $cleanQr]);
                    if (!$gudangJadi) {
                        $gudangJadi = TrnGudangJadi::find()->where(['qr_code' => $m->qr_code])->one();
                    }
                }

                // Update locs_code in trn_gudang_jadi
                if ($gudangJadi !== null) {
                    $gudangJadi->locs_code = $targetLocModel->locs_code;
                    $gudangJadi->updated_at = $now;
                    $gudangJadi->updated_by = $userId;
                    $gudangJadi->save(false, ['locs_code', 'updated_at', 'updated_by']);

                    if (empty($m->id_trn_gudang_jadi)) {
                        $m->id_trn_gudang_jadi = $gudangJadi->id;
                    }
                }

                // Insert Detail
                $moveDtl = new WmsMoveLocationDtl();
                $moveDtl->moved_move_code = $moveCode;
                $moveDtl->moved_id_stok = $gudangJadi ? $gudangJadi->id : ($m->id_trn_gudang_jadi ?: $m->id);
                if (!$moveDtl->save(false)) {
                    throw new \Exception('Gagal menyimpan detail perpindahan lokasi.');
                }

                // Update locs_code in opname pcs
                $m->locs_code = $targetLocModel->locs_code;
                $m->updated_at = $now;
                $m->updated_by = $userId;
                $m->save(false, ['locs_code', 'id_trn_gudang_jadi', 'updated_at', 'updated_by']);
            }

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Berhasil memindahkan ' . count($models) . " item ke lokasi {$targetLocModel->locs_code} dan menyinkronkan data Gudang Jadi (No. Move: {$moveCode})."
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindahkan lokasi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Render list of pcs items for a given opname_code, locs_code, motif, color, grade & status via AJAX modal
     * @return string
     */
    public function actionListPcsAjax()
    {
        $opname_code = Yii::$app->request->get('opname_code');
        $locs_code = Yii::$app->request->get('locs_code');
        $motif = Yii::$app->request->get('motif');
        $color = Yii::$app->request->get('color');
        $grade = Yii::$app->request->get('grade');
        $status = Yii::$app->request->get('status');

        $query = TrnGudangJadiOpnamePcs::find()
            ->alias('t')
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id');

        if ($opname_code !== null && $opname_code !== '') {
            $query->andWhere(['t.opname_code' => $opname_code]);
        }

        if ($locs_code !== null && $locs_code !== '') {
            $query->andWhere(['t.locs_code' => $locs_code]);
        }

        if ($grade !== null && $grade !== '') {
            $query->andWhere(['t.grade' => (int)$grade]);
        }

        if ($status !== null && $status !== '') {
            $query->andWhere(['t.status' => (int)$status]);
        }

        if (!empty($color) && $color !== '-') {
            $query->andWhere(['gj.color' => $color]);
        } elseif ($color === '-') {
            $query->andWhere(['or', ['gj.color' => null], ['gj.color' => '']]);
        }

        if (!empty($motif) && $motif !== '-') {
            $query->andWhere(['or',
                ['g_group.nama_kain' => $motif],
                ['g_mst.nama_kain' => $motif],
                ['t.qr_code_desc' => $motif]
            ]);
        }

        $models = $query->orderBy(['t.id' => SORT_ASC])->all();

        return $this->renderAjax('_list_pcs_modal', [
            'models' => $models,
            'groupInfo' => [
                'opname_code' => $opname_code,
                'locs_code' => $locs_code,
                'motif' => $motif,
                'color' => $color,
                'grade' => $grade,
                'status' => $status,
            ]
        ]);
    }

    /**
     * Print Lembar Palet per Lokasi (Format 10 Kolom Piece Length, Grade & Total)
     * @param string $locs_code
     * @param string|null $opname_code
     * @return string
     */
    public function actionPrintLokasi($locs_code, $opname_code = null)
    {
        $query = TrnGudangJadiOpnamePcs::find()
            ->alias('t')
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id');

        if ($locs_code !== null && $locs_code !== '') {
            $query->andWhere(['t.locs_code' => $locs_code]);
        }

        if ($opname_code !== null && $opname_code !== '') {
            $query->andWhere(['t.opname_code' => $opname_code]);
        }

        $models = $query->orderBy(['t.id' => SORT_ASC])->all();

        // Grouping data by (motif, color, grade)
        $groupsMap = [];
        $notes = [];
        $totalSummary = [
            'total_pcs' => count($models),
            'total_qty' => 0,
            'grades' => []
        ];

        foreach ($models as $m) {
            $motif = $m->motif;
            $color = $m->color;
            $gradeName = $m->gradeName;
            $qty = (float)$m->qty;

            // Kumpulkan catatan jika ada note / remark / hasil pemotongan
            if (!empty($m->remark)) {
                $notes[] = $m->remark;
            }
            if ($m->gudangJadi && !empty($m->gudangJadi->note)) {
                $notes[] = $m->gudangJadi->note;
            }

            $groupKey = $motif . '||' . $color . '||' . $gradeName;

            if (!isset($groupsMap[$groupKey])) {
                $groupsMap[$groupKey] = [
                    'motif' => $motif,
                    'color' => $color,
                    'grade_name' => $gradeName,
                    'pieces' => [],
                    'total_pcs' => 0,
                    'total_qty' => 0,
                ];
            }

            $groupsMap[$groupKey]['pieces'][] = [
                'id' => $m->id,
                'qty' => $qty,
                'qr_code' => $m->qr_code,
            ];
            $groupsMap[$groupKey]['total_pcs']++;
            $groupsMap[$groupKey]['total_qty'] += $qty;

            $totalSummary['total_qty'] += $qty;
            if (!isset($totalSummary['grades'][$gradeName])) {
                $totalSummary['grades'][$gradeName] = ['pcs' => 0, 'qty' => 0];
            }
            $totalSummary['grades'][$gradeName]['pcs']++;
            $totalSummary['grades'][$gradeName]['qty'] += $qty;
        }

        $notes = array_values(array_unique($notes));

        return $this->render('print-lokasi', [
            'locs_code' => $locs_code,
            'opname_code' => $opname_code,
            'groups' => array_values($groupsMap),
            'totalSummary' => $totalSummary,
            'notes' => $notes,
        ]);
    }

    /**
     * Finds the TrnGudangJadiOpnamePcs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGudangJadiOpnamePcs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGudangJadiOpnamePcs::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Data Stok Opname tidak ditemukan.');
    }
}
