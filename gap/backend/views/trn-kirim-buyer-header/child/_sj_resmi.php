<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use yii\helpers\Html;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">SURAT JALAN RESMI</h3>
        <div class="box-tools pull-right">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDiv("SjResmi")'])?>
        </div>
    </div>
    <div class="box-body" id="SjResmi">
        <table width="100%">
            <tr>
                <td width="40%">
                    <strong><?=Yii::$app->params['company']['nama']?></strong>
                    <br><?=Yii::$app->params['company']['alamat']?>
                </td>
                <td width="20%"><strong>SURAT JALAN</strong></td>
                <td width="40%" style="text-align: right;"><strong>GAP-FRM-GJ-03</strong></td>
            </tr>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td width="70%">
                    <table width="100%">
                        <tr>
                            <td width="10%">Kepada</td>
                            <td width="3%">:</td>
                            <td><?=$model->nama_buyer?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td><?=$model->alamat_buyer?></td>
                        </tr>
                    </table>
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr>
                            <td width="15%">No. SJ</td>
                            <td width="3%">:</td>
                            <td><?=$formatter->asText($model->no)?></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><?=$formatter->asDate($model->date)?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p></p>
        <p><strong>Mohon Diterima Dengan Baik Barang Dibawah Ini Dengan Kendaraan No <?=$model->plat_nomor?></strong></p>
        <p></p>

        <table width="100%" border="1">
            <thead>
            <tr>
                <th width="3%" rowspan="2" style="text-align: center;">NO</th>
                <th rowspan="2" style="text-align: center;">JENIS BARANG</th>
                <th colspan="2" style="text-align: center;">BANYAK</th>
                <th rowspan="2" style="text-align: center;">KETERANGAN</th>
            </tr>
            <tr>
                <th style="text-align: center;">SATUAN 1</th>
                <th style="text-align: center;">SATUAN 2</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $totalPcs = 0;
            $totalQty = 0;
            ?>
            <?php $i = 1; foreach ($dataProviderKirimBuyer->models as $kirimBuyerModel):?>
                <?php
                /* @var $kirimBuyerModel TrnKirimBuyer*/
                $modelsKirimBuyerItem = $kirimBuyerModel->trnKirimBuyerItems;
                ?>
                <tr>
                    <td style="text-align: center;"><?=$i?></td>
                    <td><?=$kirimBuyerModel->nama_kain_alias?></td>
                    <td>
                        <?php
                        $ct = count($modelsKirimBuyerItem);
                        $totalPcs += $ct;
                        echo $formatter->asDecimal($ct).' PCS';
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $kirimBuyerModel->getTrnKirimBuyerItems()->sum('qty');
                        $totalQty += $qty;
                        echo $formatter->asDecimal($qty).' '.MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit];
                        ?>
                    </td>
                    <td><?=$kirimBuyerModel->note?></td>
                </tr>
                <?php $i++; endforeach;?>
            <tr>
                <td></td>
                <td></td>
                <th><?=$formatter->asDecimal($totalPcs)?> PCS</th>
                <th><?=$formatter->asDecimal($totalQty)?> <?=MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit]?></th>
                <td></td>
            </tr>
            </tbody>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td style="text-align: center;" width="40%">
                    <p><strong>Penerima</strong></p><br><br><br>
                    <?=$model->penerima?>
                </td>
                <td style="text-align: center;" width="30%">
                    <p><strong>Ka. Gudang</strong></p><br><br><br>
                    <?=$model->kepala_gudang?>
                </td>
                <td style="text-align: center;" width="30%">
                    <p><strong>Hormat Kami</strong></p><br><br><br>
                    <?= $model->pengirim?>
                </td>
            </tr>
        </table>
    </div>
</div>
