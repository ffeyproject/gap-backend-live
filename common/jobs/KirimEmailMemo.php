<?php 


// File: common/jobs/KirimEmailMemo.php

namespace common\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;

class KirimEmailMemo extends BaseObject implements JobInterface
{
    public $modelId;
    public $emails;

    public function execute($queue)
    {
        $model = \common\models\ar\TrnWoMemo::findOne($this->modelId);
        if (!$model) {
            return false;
        }

        $wo = $model->wo;

        $content = Yii::$app->controller->renderPartial('@backend/views/trn-wo-memo/_print', [
            'model' => $model,
            'wo' => $wo
        ]);

        $safeWoNo = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $wo->no);
        $pdfPath = Yii::getAlias('@runtime/wo-memo_' . $model->id . '_' . $safeWoNo . '_' . time() . '.pdf');
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
        

        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
            'format' => \kartik\mpdf\Pdf::FORMAT_FOLIO,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_FILE,
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

        Yii::$app->mailer->compose()
            ->setFrom('infogajahapp@gmail.com')
            ->setTo($this->emails)
            ->setSubject('Memo Working Order No. ' . $model->no)
            ->setTextBody('Silahkan lihat lampiran Memo Working Order dalam bentuk PDF dan bisa untuk di Download.')
            ->setHtmlBody('<p>Silahkan lihat lampiran Memo Working Order dalam bentuk PDF dan bisa untuk di Download.</p><br><hr style="border: 1px solid black;"/><br><p>* Dikirim otomatis oleh Sistem Aplikasi</p>')
            ->attach($pdfPath)
            ->send();

        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }
}



?>