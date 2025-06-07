<?php 


// File: common/jobs/KirimEmailJob.php

namespace common\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;

class KirimEmailJob extends BaseObject implements JobInterface
{
    public $modelId;
    public $emails;

    public function execute($queue)
    {
        $model = \common\models\ar\TrnWo::findOne($this->modelId);
        if (!$model) {
            return false;
        }

        $mo = $model->mo;
        $scGreige = $model->scGreige;

        $content = Yii::$app->controller->renderPartial('@backend/views/trn-wo/print/print', [
            'model' => $model,
            'mo' => $mo,
            'scGreige' => $scGreige
        ]);

        $pdfPath = Yii::getAlias('@runtime/wo_' . $model->id . '_' . time() . '.pdf');
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
            'format' => \kartik\mpdf\Pdf::FORMAT_FOLIO,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_FILE,
            'content' => $content,
            'cssInline' => 'body { font-size: 10px; }',
            'filename' => $pdfPath,
        ]);
        $pdf->render();

        Yii::$app->mailer->compose()
            ->setFrom('infogajahapp@gmail.com')
            ->setTo($this->emails)
            ->setSubject('Working Order No. ' . $model->no)
            ->setTextBody('Silahkan lihat lampiran Working Order dalam bentuk PDF dan bisa untuk di Download.')
            ->setHtmlBody('<p>Silahkan lihat lampiran Working Order dalam bentuk PDF dan bisa untuk di Download.</p><br><hr style="border: 1px solid black;"/><br><p>* Dikirim otomatis oleh Sistem Aplikasi</p>')
            ->attach($pdfPath)
            ->send();

        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }
}



?>