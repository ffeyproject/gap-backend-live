<?php
/* @var $model common\models\ar\TrnGudangJadi */

use backend\components\Converter;

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

  $img_style = $model['param1'] == 1 ? "height: 150px; width: 150px; margin: 0px;" : "height: 160px; width: 160px; margin: 0px;";
?>

<table >
  <tbody>
  <tr>
      <td style="width: 50%; height: 100%; padding: 1rem 0.5rem 1rem 1rem; text-align: center;">
        <img class="img-fluid" style="<?= $img_style ?>" src="<?='qrcode/'.$model['qr_code'].'.png'?>" alt="" id="<?= mt_rand(); ?>">
        <?php
            if ($model['param1'] == 1) {
              echo '
                <p style="font-family: Calibri; font-size: 5px;"><b>&nbsp;</b></p>
                <p style="font-family: Calibri; font-size: 13px;"><b>MADE IN INDONESIA</b></p>
              ';
            }
        ?>
      </td>
      <td style="width: 50%; height: 100%; padding: 1rem 0.5rem 1rem 1rem;">
        <p style="font-family: Calibri; font-size: 10px; <?= $model['param2'] == 1 ? 'color: #000;' : 'color: #fff'?>"><b>REGISTRASI K3L</b><span style="color: #fff">Lorem ipsum</span></p>
        <p style="font-family: Calibri; font-size: 10px; <?= $model['param2'] == 1 ? 'color: #000;' : 'color: #fff'?>"><b><?= $model['k3l_code'] ?></b></p>
        <?php if ($model['param2'] == 1) { echo '<br>'; } ?>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= $model['no_wo'] ?></b></p>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= str_replace(' ', '&nbsp;', rtrim($line1, ' ')) ?></b></p>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= strlen($line2) > 0 ? str_replace(' ', '&nbsp;', rtrim($line2, ' ')) : '&nbsp;' ?></b></p>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= str_replace(' ', '&nbsp;', rtrim($line12, ' ')) ?></b></p>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= strlen($line22) > 0 ? str_replace(' ', '&nbsp;', rtrim($line22, ' ')) : '&nbsp;' ?></b></p>
        <p style="font-family: Calibri; font-size: 12px;"><b><?= $model['no_lot'] ?></b></p>
        <p style="font-family: Calibri; font-size: 20px;"><b><?= $model['length'] ?></b></p>
        <p style="font-family: Calibri; font-size: 13px;"><b><?= $model['grade'] ?></b></p>
      </td>
    </tr>
  </tbody>
</table>
