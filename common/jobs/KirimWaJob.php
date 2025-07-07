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

        // Render content PDF
        // $content = Yii::$app->controller->renderPartial('@backend/views/trn-wo/print/print', [
        //     'model' => $model,
        //     'mo' => $mo,
        //     'scGreige' => $scGreige
        // ]);

        $content = Yii::$app->view->render('@backend/views/trn-wo/print/print', [
            'model' => $model,
            'mo' => $mo,
            'scGreige' => $scGreige
        ]);

        // Pastikan folder uploads/order ada
        $orderFolder = Yii::getAlias('@backend/web/uploads/order');
        if (!file_exists($orderFolder)) {
            mkdir($orderFolder, 0777, true);
        }

        // Sanitize no WO untuk filename agar aman
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

        // URL manual ke file — tanpa urlManager
        $fileUrl = 'http://live.produksionline.xyz/backend/web/uploads/order/' . $pdfFilename;

        // Kirim ke semua nomor
        foreach ($this->numbers as $number) {
            $wa = new WhacenterService;
            $result = $wa->to($number)
                         ->line("Halo, ini WO nomor: {$model->no}, silakan cek file PDF berikut.")
                         ->sendFile($fileUrl);

            Yii::info("Kirim ke {$number} => " . json_encode($result), 'application');
        }

        // Hapus file setelah selesai kirim
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        return true;
    }
}