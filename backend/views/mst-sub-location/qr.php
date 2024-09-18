<?php
/* @var $model common\models\ar\MstSubLocation */

use backend\components\Converter;
use yii\helpers\Html;

$model = $model;
$locs_code = $model['locs_code'];
$locs_description = $model['locs_description'];

$formatter = Yii::$app->formatter;
include "../components/phpqrcode/qrlib.php";

QRcode::png($locs_code,'qrcode/'.$locs_code.'.png', 'L', 4, 0);

?>
<table>
  <tbody>
    <tr>
      <td style="width: 100%; height: 100%; padding: 1rem 0.5rem 1rem 1rem; text-align: center;" id="<?= mt_rand() ?>">
        <img class="img-fluid" style="height: 150px; width: 150px; margin: 0px;" src="<?='qrcode/'.$locs_code.'.png'?>" alt="" id="<?= mt_rand(); ?>">
        <?php
          echo '
            <p style="font-family: Calibri; font-size: 5px;"><b>&nbsp;</b></p>
            <p style="font-family: Calibri; font-size: 13px;"><b>'.$locs_code.'</b></p>
          ';
        ?>
      </td>
    </tr>
  </tbody>
</table>
