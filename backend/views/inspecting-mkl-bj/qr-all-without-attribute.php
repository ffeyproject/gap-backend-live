<?php
/* @var $model common\models\ar\TrnInspecting */

use backend\components\Converter;
include "../components/phpqrcode/qrlib.php";

$formatter = Yii::$app->formatter;
?>

<table>
    <?php foreach ($model as $m): ?>
    <tbody>
        <?php 
      $completeQrCode = '['.$m['qr_code'].']'.$m['qr_code_desc'];
      QRcode::png($completeQrCode,'qrcode/'.$m['qr_code'].'.png', 'L', 4, 0);

      $sentence = $m['is_design_or_artikel'];
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

      $sentence2 = $m['color'];
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
        <tr style="width: 100%;">
            <td style="width: 50%; height: 100%; padding: 1rem 0.5rem 1rem 1rem; text-align: center;"
                id="<?= mt_rand() ?>">
                <img class="img-fluid" style="height: 150px; width: 150px; margin: 0px;"
                    src="<?='qrcode/'.$m['qr_code'].'.png'?>" alt="" id="<?= mt_rand() ?>">
            </td>
            <td style="width: 50%; height: 100%; padding: 1rem 1rem 1rem 0.5rem;" id="<?= mt_rand() ?>">
                <p style="font-family: Calibri; font-size: 3px; color: #fff;" id="<?= mt_rand() ?>">
                    <b><?= "REGISTRASI K3L" ?></b><span style="color: #fff; font-family: Calibri; font-size: 6px;">Lorem
                        ipsum dolor sit amet consectetur adipisicin. </span>
                </p>
                <p style="font-family: Calibri; font-size: 18px;" id="<?= mt_rand() ?>"><b><?= $m['no_wo'] ?></b></p>
                <p style="font-family: Calibri; font-size: 12px;" id="<?= mt_rand() ?>">
                    <b><?= str_replace(' ', '&nbsp;', rtrim($line1, ' ')) ?></b>
                </p>
                <p style="font-family: Calibri; font-size: 12px;" id="<?= mt_rand() ?>">
                    <b><?= strlen($line2) > 0 ? str_replace(' ', '&nbsp;', rtrim($line2, ' ')) : '&nbsp;' ?></b>
                </p>

                <p style="font-family: Calibri; font-size: 12px;" id="<?= mt_rand() ?>"><b><?= $m['no_lot'] ?></b></p>

                <p style="font-family: Calibri; font-size: 18px;" id="<?= mt_rand() ?>">
                    <b><?= str_replace(' ', '&nbsp;', rtrim($line12, ' ')) ?></b>
                </p>
                <p style="font-family: Calibri; font-size: 18px;" id="<?= mt_rand() ?>">
                    <b><?= strlen($line22) > 0 ? str_replace(' ', '&nbsp;', rtrim($line22, ' ')) : '&nbsp;' ?></b>
                </p>

                <p style="font-family: Calibri; font-size: 18px;" id="<?= mt_rand() ?>"><b><?= $m['length'] ?></b></p>
                <p style="font-family: Calibri; font-size: 13px;" id="<?= mt_rand() ?>"><b><?= $m['grade'] ?></b></p>
                <p style="font-family: Calibri; font-size: 11px;"><b>NO CLAIM AFTER CUTTING</b></p>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>