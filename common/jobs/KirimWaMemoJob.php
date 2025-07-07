<?php

namespace common\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;
use kartik\mpdf\Pdf;
use common\components\WhacenterService;
use common\models\ar\TrnWoMemo;

class KirimWaMemoJob extends BaseObject implements JobInterface
{
    public $modelId;       // <-- properti disamakan dengan di action
    public $numbers = [];

    public function execute($queue)
    {
        $model = TrnWoMemo::findOne($this->modelId);
        if (!$model) {
            Yii::info("Memo ID {$this->modelId} tidak ditemukan.", 'application');
            return false;
        }


        $wo = $model->wo;

        // Buat konten HTML-nya
        $content = '
        <table>
            <tr>
                <td><strong>PT. GAJAH ANGKASA PERKASA</strong></td>
                <td></td>
                <td class="text-right"><strong>NO: ' . $model->no . '</strong></td>
            </tr>
        </table>
        <br><br>
        <div class="text-center"><strong>MEMO PEMBERITAHUAN</strong></div>
        <br><br>';

        foreach ($wo->trnWoMemos as $memo) {
            $content .= '<p>' . $memo->memo . '</p>';
        }

        $content .= '
        <br><br>
        <table class="signature">
            <tr>
                <td>Marketing</td>
                <td>Mengetahui</td>
                <td>Bandung, ' . Yii::$app->formatter->asDate($model->created_at) . '<br>Mengetahui</td>
            </tr>
            <tr>
                <td>' . $wo->marketingName . '</td>
                <td>' . $wo->mengetahuiName . '</td>
                <td>' . $wo->mengetahuiName . '</td>
            </tr>
        </table>';

        // Folder uploads/memo
        $memoFolder = Yii::getAlias('@frontend/web/uploads/memo');
        if (!file_exists($memoFolder)) {
            mkdir($memoFolder, 0777, true);
        }

        // Buat nama file PDF-nya
        $safeWoNo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $model->no);
        $pdfFilename = 'memo_' . $safeWoNo . '_' . time() . '.pdf';
        $pdfPath = $memoFolder . '/' . $pdfFilename;

        // Generate PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_FOLIO,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'content' => $content,
            'cssInline' => '
                body { font-size: 11pt; font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .signature td { text-align: center; padding-top: 50px; vertical-align: top; }
            ',
            'filename' => $pdfPath,
        ]);
        $pdf->render();

        // Tunggu file terbuat (maks 10x 0.2 detik = 2 detik)
        $waitCount = 0;
        while ((!file_exists($pdfPath) || filesize($pdfPath) < 10240) && $waitCount < 10) {
            usleep(200000);
            $waitCount++;
        }

        // URL file untuk WA
        $fileUrl = 'http://live.produksionline.xyz/uploads/memo/' . $pdfFilename;

        // Kirim ke semua nomor WA
        foreach ($this->numbers as $number) {
            $wa = new WhacenterService;
            $result = $wa->to($number)
                ->line("Memo WO No. {$model->no}, silakan lihat file PDF berikut.")
                ->sendFile($fileUrl);

            Yii::info("Kirim Memo ke {$number} => " . json_encode($result), 'application');
        }

        return true;
    }
}