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
    public $modelId;
    public $numbers = [];

    public function execute($queue)
    {
        $model = TrnWoMemo::findOne($this->modelId);
        if (!$model) {
            Yii::info("Memo ID {$this->modelId} tidak ditemukan.", 'application');
            return false;
        }

        $wo = $model->wo;

        // Render content via view file
        $view = new \yii\web\View();
        $content = $view->renderFile('@backend/views/trn-wo-memo/_print.php', [
            'model' => $model,
            'wo'    => $wo,
        ]);

        // Simpan debug content HTML (optional)
        // file_put_contents('/tmp/memo_debug.html', $content);

        // Pastikan folder uploads/memo ada
        $memoFolder = Yii::getAlias('@backend/web/uploads/memo');
        if (!file_exists($memoFolder)) {
            mkdir($memoFolder, 0777, true);
        }

        // Generate nama file PDF
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
            'filename' => $pdfPath,
        ]);
        $pdf->render();

        // Tunggu file terbentuk dengan ukuran minimal 10KB
        $waitCount = 0;
        while ((!file_exists($pdfPath) || filesize($pdfPath) < 10240) && $waitCount < 10) {
            usleep(200000);
            $waitCount++;
        }


        // URL public file untuk WA
        $fileUrl = 'http://live.produksionline.xyz/uploads/memo/' . $pdfFilename;
        Yii::info("Link Memo: {$fileUrl}", 'application');

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