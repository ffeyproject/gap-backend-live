<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * Controller untuk melakukan sinkronisasi data secara otomatis via cron job/scheduler.
 */
class SyncController extends Controller
{
    /**
     * Menjalankan semua sinkronisasi sekaligus.
     * Dipanggil dengan perintah: php yii sync
     */
    public function actionIndex()
    {
        $this->actionVerpacking();
        $this->actionInspectingStuck();
    }

    /**
     * Menjalankan sinkronisasi data verpacking (Dyeing).
     * Dipanggil dengan perintah: php yii sync/verpacking
     */
    public function actionVerpacking()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        echo "=== MEMULAI SINKRONISASI VERPACKING DYEING ===\n";
        
        // Loop secara kronologis dari Januari (1) hingga bulan saat ini
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            $queryMonthYear = "$currentYear-$monthNumber";
            $likePattern = $queryMonthYear . '-%';
            
            echo "Memproses Bulan: " . date('F Y', strtotime("$currentYear-$monthNumber-01")) . "...\n";
            
            // Cari data Process ID 1 untuk bulan terpilih
            $processes = \common\models\ar\KartuProcessDyeingProcess::find()
                ->where(['process_id' => 1])
                ->andWhere(new \yii\db\Expression("CAST(value AS jsonb)->>'tanggal' LIKE :tgl", [':tgl' => $likePattern]))
                ->all();

            $kartuIds = [];
            foreach ($processes as $proc) {
                $kartuIds[] = $proc->kartu_process_id;
            }

            if (!empty($kartuIds)) {
                // Cari kartu proses yang belum disinkronkan (approved_at kosong)
                $cards = \common\models\ar\TrnKartuProsesDyeing::find()
                    ->where(['id' => $kartuIds])
                    ->andWhere(['or', ['approved_at' => null], ['approved_at' => 0]])
                    ->all();

                $updatedCount = 0;
                foreach ($cards as $card) {
                    $log = \common\models\ar\ActionLogKartuDyeing::find()
                        ->where(['kartu_proses_id' => $card->id])
                        ->orderBy(['created_at' => SORT_ASC])
                        ->one();

                    if ($log) {
                        $card->approved_at = $log->created_at;
                        $card->approved_by = $log->user_id; // opsional
                        if ($card->save(false)) { // skip validasi untuk melewati constraint lain
                            $updatedCount++;
                        }
                    }
                }
                echo "-> Berhasil memperbarui $updatedCount kartu proses.\n";
            } else {
                echo "-> Tidak ada data Buka Greige pada bulan ini.\n";
            }
        }
        echo "=== SINKRONISASI VERPACKING DYEING SELESAI ===\n\n";
    }

    /**
     * Menjalankan sinkronisasi status stuck inspecting MKL BJ.
     * Dipanggil dengan perintah: php yii sync/inspecting-stuck
     */
    public function actionInspectingStuck()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        echo "=== MEMULAI SINKRONISASI INSPECTING MKL BJ STUCK ===\n";
        
        // Loop secara kronologis dari Januari (1) hingga bulan saat ini
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            $startDate = "$currentYear-$monthNumber-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            echo "Memproses Periode: " . date('F Y', strtotime($startDate)) . "...\n";
            
            $query = \common\models\ar\InspectingMklBj::find()
                ->with('items')
                ->where(['in', 'status', [\common\models\ar\InspectingMklBj::STATUS_POSTED, \common\models\ar\InspectingMklBj::STATUS_POSTED_PARTIAL]])
                ->andWhere(['between', 'tgl_kirim', $startDate, $endDate]);
                
            $models = $query->all();
            $fixed = 0;
            
            $allItemIds = [];
            foreach ($models as $model) {
                foreach ($model->items as $item) {
                    $allItemIds[] = $item->id;
                }
            }
            
            $allReceivedItemIds = [];
            if (!empty($allItemIds)) {
                $allReceivedItemIds = \common\models\ar\TrnGudangJadi::find()
                    ->select('id_from')
                    ->where(['id_from' => $allItemIds, 'trans_from' => 'MKL'])
                    ->column();
                $allReceivedItemIds = array_flip($allReceivedItemIds);
            }

            foreach ($models as $model) {
                $joinPieceHasReceived = [];
                foreach ($model->items as $ii) {
                    if (!empty($ii->join_piece) && isset($allReceivedItemIds[$ii->id])) {
                        $joinPieceHasReceived[$ii->join_piece] = true;
                    }
                }

                $allReceived = true;
                $receivedCount = 0;
                $totalItemsCount = 0;
                foreach ($model->items as $item) {
                    if($item->is_head == 1 && $item->qty > 0){
                        $totalItemsCount++;
                        $isReceived = isset($allReceivedItemIds[$item->id]);
                        if (!$isReceived && !empty($item->join_piece) && isset($joinPieceHasReceived[$item->join_piece])) {
                            $isReceived = true;
                        }
                        if($isReceived){
                            $receivedCount++;
                        } else {
                            $allReceived = false;
                        }
                    }
                }

                $targetStatus = \common\models\ar\InspectingMklBj::STATUS_POSTED;
                if ($allReceived) {
                    $targetStatus = \common\models\ar\InspectingMklBj::STATUS_DELIVERED;
                } elseif ($receivedCount > 0) {
                    $targetStatus = \common\models\ar\InspectingMklBj::STATUS_POSTED_PARTIAL;
                }

                if ($model->status !== $targetStatus) {
                    $model->status = $targetStatus;
                    if ($targetStatus === \common\models\ar\InspectingMklBj::STATUS_DELIVERED) {
                        if(!$model->delivered_at) {
                            $model->delivered_at = time();
                        }
                        if(!$model->delivered_by) {
                            $model->delivered_by = 1; // Default sistem user id 1
                        }
                        $model->save(false, ['status', 'delivered_at', 'delivered_by']);
                    } else {
                        $model->save(false, ['status']);
                    }
                    $fixed++;
                }
            }
            echo "-> Memeriksa " . count($models) . " data. Berhasil memperbaiki status $fixed data yang stuck.\n";
        }
        echo "=== SINKRONISASI INSPECTING MKL BJ STUCK SELESAI ===\n\n";
    }
}
