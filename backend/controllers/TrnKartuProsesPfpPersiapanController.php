<?php

namespace backend\controllers;

use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesPfpSearch;
use common\models\ar\TrnStockGreige;
use Yii;

class TrnKartuProsesPfpPersiapanController extends TrnKartuProsesPfpController
{
    /**
     * Lists all TrnKartuProsesPfp models for Gudang Persiapan PFP.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPfpSearch(['jenis_gudang' => TrnStockGreige::JG_PFP_PERSIAPAN]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TrnKartuProsesPfp model with default asal_greige = Mutasi.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKartuProsesPfp([
            'date' => date('Y-m-d'),
            'note' => '-',
            'asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $orderPfp = $model->orderPfp;

            if($orderPfp->status != $orderPfp::STATUS_APPROVED){
                Yii::$app->session->setFlash('error', 'Status order pfp tidak valid, kartu proses tidak bisa dibuat.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            /**
             * Periksa berapa banyak Order PFP yang sudah dibuat kartu prosesnya
             * kalau sudah mencukupi, jangan ijinkan pembuatan ini
            */
            $jumlahKartuProses = $orderPfp->getTrnKartuProsesPfps()
                ->andWhere(['!=', 'status', TrnKartuProsesPfp::STATUS_GAGAL_PROSES])
                ->count('id');

            if ($jumlahKartuProses >= $orderPfp->qty) {
                $model->addError('order_pfp_id', 'Order PFP ini sudah tercukupi kartu prosesnya, sudah dibuat sebanyak '.$jumlahKartuProses.' kartu proses.');
                return $this->render('create', ['model' => $model]);
            }

            // periksa apakah nomor kartu dan motif sudah pernah dibuat atau belum, jika sudah pernah, maka diblokir
            $qMotifNoKartuExists = clone \common\models\ar\TrnKartuProsesPfp::find()
                ->where(['greige_id'=>$orderPfp->greige_id, 'nomor_kartu'=>$model->nomor_kartu]);
            if($qMotifNoKartuExists->exists()){
                Yii::$app->session->setFlash('error', 'Nomor kartu dan motif tidak valid.');
                $model->addError('nomor_kartu', 'Nomor kartu dan motif tidak valid.');
                return $this->render('create', ['model' => $model]);
            }

            $handling = $orderPfp->handling;
            $model->handling = $handling->name;
            $model->lebar_preset = $handling->lebar_preset;
            $model->lebar_finish = $handling->lebar_finish;
            $model->berat_finish = $handling->berat_finish;
            $model->t_density_lusi = $handling->densiti_lusi;
            $model->t_density_pakan = $handling->densiti_pakan;

            $model->greige_group_id = $orderPfp->greige_group_id;
            $model->greige_id = $orderPfp->greige_id;
            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Override actionPosting for Persiapan PFP.
     * Tidak mengurangi stock ke field manapun, dan status langsung berubah ke DELIVERED.
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $greigeGroup = $model->greigeGroup;

        $perBatchHalfToleransiAtas  = 0;
        $perBatchHalfToleransiBawah = 0;

        if (!$model->no_limit_item) {
            $lenPerBatch               = (float)$greigeGroup->qty_per_batch;
            $perBatchHalf              = $lenPerBatch / 2;
            $perBatchHalfInPercent     = 0.02 * $perBatchHalf;
            $perBatchHalfToleransiAtas = $perBatchHalf + $perBatchHalfInPercent;
            $perBatchHalfToleransiBawah = $perBatchHalf - $perBatchHalfInPercent;
        }

        // Langsung DELIVERED
        $model->status = $model::STATUS_DELIVERED;
        $model->posted_at = time();
        $model->delivered_at = time();
        $model->delivered_by = Yii::$app->user->id;

        if ($model->no_urut === null) {
            $model->setNomor();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $totalTubeKiri  = 0;
            $totalTubeKanan = 0;

            foreach ($model->trnKartuProsesPfpItems as $trnKartuProsesPfpItem) {
                $stockItem = $trnKartuProsesPfpItem->stock;

                if ($stockItem->status == $stockItem::STATUS_ON_PROCESS_CARD) {
                    $panjang = Yii::$app->formatter->asDecimal($stockItem->panjang_m);
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', "Salah satu roll greige (ID:{$stockItem->id}, Panjang: {$panjang}M) sudah digunakan oleh kartu proses lain.");
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockItem->status = $stockItem::STATUS_ON_PROCESS_CARD;
                if (!$stockItem->save(false, ['status'])) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan status roll.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                switch ($trnKartuProsesPfpItem->tube) {
                    case $trnKartuProsesPfpItem::TUBE_KIRI:
                        $totalTubeKiri += (float)$stockItem->panjang_m;
                        break;
                    case $trnKartuProsesPfpItem::TUBE_KANAN:
                        $totalTubeKanan += (float)$stockItem->panjang_m;
                        break;
                }
            }

            if (!$model->no_limit_item) {
                if (($totalTubeKiri < $perBatchHalfToleransiBawah) || ($totalTubeKanan < $perBatchHalfToleransiBawah)) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah tube kiri/kanan kurang dari 1 batch dikurangi 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                if (($totalTubeKiri > $perBatchHalfToleransiAtas) || ($totalTubeKanan > $perBatchHalfToleransiAtas)) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah tube kiri/kanan melebihi 1 batch ditambah 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            // Atur Nomor Proses (Penting untuk Delivered)
            $model->setNomorProses();

            if (!$model->save(false)) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan kartu proses.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Kartu proses berhasil diposting dan langsung diterima (Delivered).');
            return $this->redirect(['view', 'id' => $model->id]);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

}
