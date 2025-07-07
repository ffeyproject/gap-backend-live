<?php
namespace common\jobs;

use common\components\WhacenterService;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\ar\TrnWo;
use Yii;
use kartik\mpdf\Pdf;

class KirimWaJob extends BaseObject implements JobInterface
{
    public $modelId;
    public $numbers = [];

    public function execute($queue)
    {
        $model = TrnWo::findOne($this->modelId);
        if (!$model) {
            Yii::info("WO ID {$this->modelId} tidak ditemukan.", 'application');
            return false;
        }

        $mo = $model->mo;
        $scGreige = $model->scGreige;

        // Render content tanpa layout
        $view = new \yii\web\View();
        $content = $view->renderFile('@backend/views/trn-wo/print/print.php', [
            'model' => $model,
            'mo' => $mo,
            'scGreige' => $scGreige,
        ]);

        // file_put_contents('/tmp/debug_content.html', $content); // untuk debug kalau perlu

        // Pastikan folder uploads/order ada
        $orderFolder = Yii::getAlias('@backend/web/uploads/order');
        if (!file_exists($orderFolder)) {
            mkdir($orderFolder, 0777, true);
        }

        // Generate filename PDF
        $safeNo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $model->no);
        $pdfFilename = $safeNo . '_' . time() . '.pdf';
        $pdfPath = $orderFolder . '/' . $pdfFilename;

        // Generate PDF ke file
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_FOLIO,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'content' => $content,
            'cssInline' => 'body { font-size: 10px; }',
            'filename' => $pdfPath,
        ]);
        $pdf->render();

        // Tunggu file benar-benar dibuat
        $waitCount = 0;
        while ((!file_exists($pdfPath) || filesize($pdfPath) < 10240) && $waitCount < 10) {
            usleep(200000);
            $waitCount++;
        }

        // URL file public frontend
        $fileUrl = 'http://live.produksionline.xyz/uploads/order/' . $pdfFilename;

        // Kirim WA ke nomor
        foreach ($this->numbers as $number) {
            $wa = new WhacenterService;
            $result = $wa->to($number)
                         ->line("Halo, ini WO nomor: {$model->no}, silakan cek file PDF berikut.")
                         ->sendFile($fileUrl);

            Yii::info("Kirim ke {$number} => " . json_encode($result), 'application');
        }

        return true;
    }
}