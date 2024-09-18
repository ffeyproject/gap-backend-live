<?php

use common\models\ar\TrnStockGreige;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\rekap\LaporanStockSearch */
/* @var $datas array */

$this->title = 'Laporan Stock Greige';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('.table > thead > tr > th {
    vertical-align: middle;
    border-bottom: 2px solid #ddd;
    text-align: center;
}');
?>
<div class="trn-stock-greige-index">
    <?php echo $this->render('_search-laporan-stock', ['model' => $searchModel]); ?>

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Data</strong> <strong class="pull-right">Periode: <?=$searchModel->dateRange?></strong></div>

        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th rowspan="2">Motif</th>
                    <td colspan="2" style="text-align: center;"><strong>Lot</strong></td>
                    <th rowspan="2">Keterangan</th>
                    <th rowspan="2">Kondisi Greige</th>
                    <th rowspan="2">Lebar Kain</th>
                    <th rowspan="2">Jumlah Stock</th>
                    <td colspan="6" style="text-align: center;"><strong>Saldo Akhir</strong></td>
                </tr>
                <tr>
                    <th>Lusi</th>
                    <th>Pakan</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>X</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($datas as $data):?>
                    <?php
                    $motif = $data['motif'];
                    $jumlahStock = $data['jumlah_stock'];

                    unset($data['date']);
                    unset($data['motif']);
                    unset($data['jumlah_stock']);

                    $cd = count($data);
                    ?>

                    <?php $i=0; foreach ($data as $datum):?>
                        <?php if($i===0):?>
                            <tr>
                                <td rowspan="<?=$cd?>" style="vertical-align: middle;"><?=$motif?></td>
                                <td style="vertical-align: middle;"><?=$datum['lot_lusi']?></td>
                                <td style="vertical-align: middle;"><?=$datum['lot_pakan']?></td>
                                <td style="vertical-align: middle;"><?=$datum['keterangan']?></td>
                                <td style="vertical-align: middle;"><?=$datum['kondisi_greige']?></td>
                                <td style="vertical-align: middle;"><?=$datum['lebar_kain']?></td>
                                <td rowspan="<?=$cd?>" style="text-align: right; vertical-align: middle;"><?=$jumlahStock?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['a']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['b']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['c']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['d']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['ng']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['total']?></td>
                            </tr>
                        <?php else:?>
                            <tr>
                                <td style="vertical-align: middle;"><?=$datum['lot_lusi']?></td>
                                <td style="vertical-align: middle;"><?=$datum['lot_pakan']?></td>
                                <td style="vertical-align: middle;"><?=$datum['keterangan']?></td>
                                <td style="vertical-align: middle;"><?=$datum['kondisi_greige']?></td>
                                <td style="vertical-align: middle;"><?=$datum['lebar_kain']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['a']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['b']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['c']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['d']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['ng']?></td>
                                <td style="text-align: right; vertical-align: middle;"><?=$datum['total']?></td>
                            </tr>
                        <?php endif;?>
                        <?php $i++; endforeach;?>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php //BaseVarDumper::dump($datas, 10, true);?>