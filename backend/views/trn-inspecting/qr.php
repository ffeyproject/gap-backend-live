<?php
/* @var $model common\models\ar\TrnInspecting */

use backend\components\Converter;
use yii\helpers\Html;

$formatter = Yii::$app->formatter;
include "../components/phpqrcode/qrlib.php";

$completeQrCode = '['.$model['qr_code'].']'.$model['qr_code_desc'];

QRcode::png($completeQrCode,'qrcode/'.$model['qr_code'].'.png', 'L', 4, 0);

$sentence = $model['is_design_or_artikel'];
$words = explode(' ', $sentence);
$line1 = ''; $line2 = '';
foreach ($words as $word) {
  $lengthWithWord = strlen($line1 . $word . ' ');
  if ($lengthWithWord <= 21) {
    $line1 .= $word . ' ';
  } else {
    $line2 .= $word . ' ';
  }
}

$img_style = $model['param3'] == 1 ? "height: 125px; width: 125px; margin: 0px;" : "height: 135px; width: 135px; margin: 0px;";

$sentence2 = $model['color'];
$words2 = explode(' ', $sentence2);
$line12 = ''; $line22 = '';
foreach ($words2 as $word2) {
  $lengthWithWord2 = strlen($line12 . $word2 . ' ');
  if ($lengthWithWord2 <= 21) {
    $line12 .= $word2 . ' ';
  } else {
    $line22 .= $word2 . ' ';
  }
}
?>
<table>
    <tbody>
        <tr>
            <td style="width: 38%; height: 100%; padding: 0.2rem 0.1rem 0.2rem 0.4rem; text-align: center; vertical-align: middle;"
                id="<?= mt_rand() ?>">
                <img src="<?= Yii::getAlias('@webroot') . '/images/logo/logo-halal-gap.jpeg' ?>" style="<?= $model['param3'] == 1 ? 'width: 125px;' : 'width: 135px;' ?> height: auto; margin-bottom: 2px;" alt="Logo Halal">
                <br>
                <img class="img-fluid" style="<?= $img_style ?>" src="<?='qrcode/'.$model['qr_code'].'.png'?>" alt=""
                    id="<?= mt_rand(); ?>">

                <?php
                if ($model['param3'] == 1) {
                  echo '<p style="font-family: Calibri; font-size: 10px; margin: 2px 0 0 0;"><b>MADE IN INDONESIA</b></p>';
                }
                ?>
            </td>
            <td style="width: 4%; height: 100%; text-align: center; vertical-align: middle; padding: 0; text-rotate: 90;" text-rotate="90">
                <p style="font-family: Calibri; font-size: 7px; margin: 0; line-height: 1; white-space: nowrap;">
                    <b>NO CLAIM AFTER CUTTING &nbsp;&nbsp; <?= $model['qr_code'] ?></b>
                </p>
            </td>
            <td style="width: 58%; height: 100%; padding: 0.2rem 0.4rem 0.2rem 0.1rem; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        <td style="width: 55%; padding: 0; vertical-align: top;">
                            <p
                                style="font-family: Calibri; font-size: 10px; margin: 0; line-height: 1.15; <?= $model['param4'] == 1 ? 'color: #000;' : 'color: #fff'?>">
                                <b>REGISTRASI K3L</b>
                            </p>
                            <p
                                style="font-family: Calibri; font-size: 10px; margin: 0; line-height: 1.15; <?= $model['param4'] == 1 ? 'color: #000;' : 'color: #fff'?>">
                                <b><?= $model['k3l_code'] ?></b>
                            </p>
                        </td>
                        <td style="width: 45%; padding: 0; text-align: right; vertical-align: top;">
                            <img src="<?= Yii::getAlias('@webroot') . '/images/logo/logo-tkdn.jpg' ?>" style="height: 20px; width: auto; vertical-align: top; margin-right: 2px;" alt="Logo TKDN"><img src="<?= Yii::getAlias('@webroot') . '/images/logo/logo-sni.jpeg' ?>" style="height: 20px; width: auto; vertical-align: top;" alt="Logo SNI">
                        </td>
                    </tr>
                </table>
                <p style="font-family: Calibri; font-size: 18px; margin: 2px 0 0 0;"><b><?= $model['no_wo'] ?></b></p>
                <p style="font-family: Calibri; font-size: 12px; margin: 0;">
                    <b><?= str_replace(' ', '&nbsp;', rtrim($line1, ' ')) ?></b>
                </p>
                <p style="font-family: Calibri; font-size: 12px; margin: 0;">
                    <b><?= strlen($line2) > 0 ? str_replace(' ', '&nbsp;', rtrim($line2, ' ')) : '&nbsp;' ?></b>
                </p>

                <p style="font-family: Calibri; font-size: 12px; margin: 0;"><b><?= $model['no_lot'] ?></b></p>

                <p style="font-family: Calibri; font-size: 18px; margin: 0;">
                    <b><?= str_replace(' ', '&nbsp;', rtrim($line12, ' ')) ?></b>
                </p>
                <p style="font-family: Calibri; font-size: 18px; margin: 0;">
                    <b><?= strlen($line22) > 0 ? str_replace(' ', '&nbsp;', rtrim($line22, ' ')) : '&nbsp;' ?></b>
                </p>

                <p style="font-family: Calibri; font-size: 18px; margin: 0;"><b><?= $model['length'] ?></b></p>
                <p style="font-family: Calibri; font-size: 13px; margin: 0;"><b><?= $model['grade'] ?></b></p>

            </td>
        </tr>
    </tbody>
</table>