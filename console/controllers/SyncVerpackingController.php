<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * Controller khusus untuk sinkronisasi data verpacking (Dyeing) secara otomatis via cron job.
 */
class SyncVerpackingController extends Controller
{
    /**
     * Menjalankan sinkronisasi data verpacking (Dyeing).
     * Dipanggil dengan perintah: php yii sync-verpacking
     */
    public function actionIndex()
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
}
