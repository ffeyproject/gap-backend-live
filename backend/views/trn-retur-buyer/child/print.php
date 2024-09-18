<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */

use common\models\User;
use yii\helpers\Html;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">BERITA ACARA</h3>
        <div class="box-tools pull-right">
            <input type="number" id="SizeText" min="1" max="99" step="1" value="11">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDiv()'])?>
        </div>
    </div>
    <div class="box-body" id="GFG">
        <div style="text-align: center;">
            <h2>BERITA ACARA RETUR</h2>
        </div>

        <table width="100%">
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td width="15%">Diterima Retur Dari</td>
                            <td width="2%">:</td>
                            <td><?=$model->customer->name?></td>
                        </tr>
                        <tr>
                            <td>Tanggal SJ Retur</td>
                            <td>:</td>
                            <td><?=Yii::$app->formatter->asDate($model->date_document)?></td>
                        </tr>
                    </table>
                </td>
                <td width="20%" style="text-align: right">
                    <span>GAP-FRM-GJ-05</span>
                </td>
            </tr>
        </table>

        <p></p>

        <table width="100%" border="1">
            <thead>
            <tr>
                <th rowspan="2" class="text-center">NOMOR SURAT JALAN</th>
                <th rowspan="2" class="text-center">NAMA BARANG</th>
                <th colspan="2" class="text-center">JUMLAH BARANG</th>
                <th rowspan="2" class="text-center">ALASAN RETUR</th>
            </tr>
            <tr>
                <th class="text-center">PCS</th>
                <th class="text-center">QTY</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?=$model->no_document?></td>
                <td><?=$model->wo->greige->nama_kain?></td>
                <td><?=Yii::$app->formatter->asDecimal($model->getTrnReturBuyerItems()->count('id'))?></td>
                <td>
                    <?=Yii::$app->formatter->asDecimal($model->getTrnReturBuyerItems()->sum('qty')).' '.\common\models\ar\MstGreigeGroup::unitOptions()[$model->unit]?>
                </td>
                <td><?=$model->note?></td>
            </tr>
            </tbody>
        </table>

        <p></p>

        <table width="100%" border="1">
            <thead>
            <tr>
                <th class="text-center">Hasil Pemeriksaan Oleh QC</th>
                <th class="text-center">Keputusan (QC & MARKETING)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="50%"></td>
                <td>
                    <ol>
                        <?php
                        foreach (\common\models\ar\TrnReturBuyer::keputusanQcOptions() as $key=>$keputusanQcOption){
                            $style = 'style="text-decoration: line-through;"';
                            if($key == $model->keputusan_qc){
                                $style = '';
                            }
                            echo '<li '.$style.'>'.$keputusanQcOption.'</li>';
                        }
                        ?>
                    </ol>
                </td>
            </tr>
            </tbody>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td style="text-align: center;" width="25%">
                    <p><strong>Penanggung Jawab</strong></p><br><br><br>
                    <?=$model->penanggungjawab?>
                </td>
                <td style="text-align: center;" width="25%">
                    <p><strong>Marketing</strong></p><br><br><br>
                    <?=$model->sc->marketingName?>
                </td>
                <td style="text-align: center;" width="25%">
                    <p><strong>QC</strong></p><br><br><br>
                    <?=$model->nama_qc?>
                </td>
                <td style="text-align: center;" width="25%">
                    <p><strong>Gudang Jadi</strong></p><br><br><br>
                    <?= User::findOne($model->created_by)->full_name?>
                </td>
            </tr>
        </table>
    </div>
</div>
