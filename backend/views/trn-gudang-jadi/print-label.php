<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use yii\helpers\Html;

$this->title = 'Print Label Palet';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php
    echo '<div class="text-right"><p>Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11"></p></div>';
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">PRATINJAU</h3>
        <div class="box-tools pull-right">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDiv("PLP")'])?>
        </div>
    </div>
    <div class="box-body" id="PLP">
        <table width="100%">
            <tr>
                <td width="50%"><strong><?= "Location: ".$header['location'] ?></strong></td>
                <td width="50%" style="text-align: right;"><strong><?= date('l, d F Y H:i:s'); ?></strong></td>
            </tr>
        </table>

        <p></p><br>

        <table width="100%" border="1">
            <thead>
                <tr>
                    <th width="3%" style="text-align: center;">NO</th>
                    <th style="text-align: center;">NO WO</th>
                    <th style="text-align: center;">NAMA BARANG</th>
                    <th style="text-align: center;">GRADE</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: center;">SATUAN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $model as $i => $m):?>
                    <tr>
                        <td class="text-center"><?=$i+1;?></td>
                        <td class="text-left"><?=$m['no_wo'];?></td>
                        <td class="text-left"><?=$m['nama_barang'];?></td>
                        <td class="text-center"><?=$m['grade'];?></td>
                        <td class="text-center"><?=$m['qty'];?></td>
                        <td class="text-center"><?=$m['unit'];?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p></p><br><br>

        <table width="100%">
            <tr>
                <td style="text-align: center;" width="30%">
                    <p><strong>Checker</strong></p><br><br><br><br><br>
                    <?= "______________________" ?>
                </td>
                <td style="text-align: center;" width="40%">
                    <p><strong>Maker</strong></p><br><br><br><br><br>
                    <?= "______________________" ?>
                </td>
                <td style="text-align: center;" width="30%">
                    <p><strong>Assign</strong></p><br><br><br><br><br>
                    <?= "______________________" ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<script type="text/javascript">
    function printDiv(div) {
        var fontSize = document.getElementById("SizeText").value;
        var divContents = document.getElementById(div).innerHTML;
        var a = window.open('', '');
        a.document.write('<html>');
        a.document.write('<head>');
        a.document.write('<style type="text/css">');
        a.document.write('body{font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 2px;} th, td {padding: 0.5em 1em;}');
        //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
        a.document.write('</style>');
        a.document.write('</head>');
        a.document.write('<body>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        a.print();
    }
</script>