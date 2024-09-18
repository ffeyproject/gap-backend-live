<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnScGreige;
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
                    <br><strong><?=Yii::$app->params['company']['alamat']?></strong>
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
                            <td width="10%"><strong>Kepada</strong></td>
                            <td width="3%"><strong></strong>:</strong></td>
                            <td><strong><?=$model->nama_buyer?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td><strong></strong>:</strong></td>
                            <td><strong><?=$model->alamat_buyer?></strong></td>
                        </tr>
                    </table>
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr>
                            <td width="15%"><strong>No. SJ</strong></td>
                            <td width="3%"><strong>:</strong></td>
                            <td><strong><?=$formatter->asText($model->no)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td><strong>:</strong></td>
                            <td><strong><?=$formatter->asDate($model->date)?></strong></td>
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
                <th rowspan="2" style="text-align: center;">KETERANGAN BARANG</th>
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
                $artikel = $kirimBuyerModel->wo->mo->article;
                $jenisProcess = $kirimBuyerModel->wo->mo->process;
                $kodeDesign = $kirimBuyerModel->wo->mo->design;

                ?>
                <tr>
                    <td style="text-align: center;"><strong><?=$i?></strong></td>
                    <td><strong><?=TrnScGreige::processOptions()[$kirimBuyerModel->wo->scGreige->process] ?> <?=MstGreigeGroup::lebarKainOptions()[$kirimBuyerModel->scGreige->greigeGroup->lebar_kain]?></strong></td>
                     <?php if ($jenisProcess == TrnScGreige::PROCESS_PRINTING || $jenisProcess == TrnScGreige::PROCESS_DIGITAL_PRINTING) { ?>
                         <td><strong><?=$artikel?> / <?= $kodeDesign ?></strong></td>
                    <?php }else{ ?>
                         <td><strong><?=$artikel?></strong></td>
                    <?php } ?>
                    <td>
                       <strong>
                         <?php
                        $ct = count($modelsKirimBuyerItem);
                        $totalPcs += $ct;
                        echo $formatter->asDecimal($ct).' PCS';
                        ?>
                       </strong>
                    </td>
                    <td>
                        <strong>
                            <?php
                        $qty = $kirimBuyerModel->getTrnKirimBuyerItems()->sum('qty');
                        $totalQty += $qty;
                        echo $formatter->asDecimal($qty, 2).' '.MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit];
                        ?>
                        </strong>
                    </td>
                    <td><strong><?=$kirimBuyerModel->note?></strong></td>
                </tr>
                <?php $i++; endforeach;?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <th><strong><?=$formatter->asDecimal($totalPcs)?> PCS</strong></th>
                <th><strong><?=$formatter->asDecimal($totalQty, 2)?> <?=MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit]?></strong></th>
                <td></td>
            </tr>
            </tbody>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td style="text-align: center;" width="40%">
                    <p><strong>Penerima</strong></p><br><br><br>
                   <strong> <?=$model->penerima?></strong>
                </td>
                <td style="text-align: center;" width="30%">
                    <p><strong>Ka. Gudang</strong></p><br><br><br>
                    <strong><?=$model->kepala_gudang?></strong>
                </td>
                <td style="text-align: center;" width="30%">
                    <p><strong>Hormat Kami</strong></p><br><br><br>
                   <strong> <?= $model->pengirim?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>