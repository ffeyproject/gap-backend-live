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

        $view = new \yii\web\View();
        $content = $view->renderFile('@backend/views/trn-wo-memo/_print.php', [
            'model' => $model,
            'wo'    => $wo,
        ]);

        // Debug content (optional)
        file_put_contents('/tmp/memo_debug_' . $this->modelId . '.html', $content);

        $memoFolder = Yii::getAlias('@backend/web/uploads/memo');
        if (!file_exists($memoFolder)) {
            mkdir($memoFolder, 0777, true);
        }

        // Generate nama file unik
        $safeWoNo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $model->no);
        $pdfFilename = 'memo_' . $safeWoNo . '_' . uniqid() . '.pdf';
        $pdfPath = $memoFolder . '/' . $pdfFilename;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_FOLIO,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'content' => $content,
            'filename' => $pdfPath,
        ]);
        $pdf->render();

        // Cek file minimal 1KB
        $waitCount = 0;
        while ((!file_exists($pdfPath) || filesize($pdfPath) < 1024) && $waitCount < 10) {
            usleep(200000);
            $waitCount++;
        }

        $fileUrl = 'http://live.produksionline.xyz/uploads/memo/' . $pdfFilename;
        Yii::info("Link Memo ID {$this->modelId}: {$fileUrl}", 'application');

        foreach ($this->numbers as $number) {
            $wa = new WhacenterService;
            $result = $wa->to($number)
                ->line("Memo WO No. {$model->no}, silakan lihat file PDF berikut.")
                ->sendFile($fileUrl);

            Yii::info("Kirim Memo ID {$this->modelId} ke {$number} => " . json_encode($result), 'application');
        }

        return true;
    }
}