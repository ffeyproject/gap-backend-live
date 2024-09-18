<?php
use common\models\ar\TrnScGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */

$modelsItem = $model->gudangJadiMutasiItems;
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <strong><u>SURAT PENGANTAR</u></strong>
    </div>
</div>

<div class="row">
    <div class="col-xs-8"></div>

    <div class="col-xs-4">
        <table style="width: 100%;">
            <tr><td>Nomor</td><td>:</td><td class="text-right"><?=$model->nomor?></td></tr>
            <tr><td>Tanggal</td><td>:</td><td class="text-right"><?=$model->date?></td></tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-5">
        <div style="border: 1px solid black; padding: 3px;">
            Pindah Dari:
            <br>----------------------------------------
            <br>Gd. Jadi
            <br><?=$model->kepala_gudang?>
        </div>
    </div>

    <div class="col-xs-2"></div>

    <div class="col-xs-5">
        <div style="border: 1px solid black; padding: 3px;">
            Pindah Ke:
            <br>----------------------------------------
            <br><?=$model->dept_tujuan?>
            <br><?=$model->penerima?>
        </div>
    </div>
</div>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>No</th>
        <th>Jenis Barang</th>
        <th>Ket. Barang</th>
        <th>Sat. 1</th>
        <th>Sat. 2</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    <?php $totalPanjang = 0; $totalPanjangYds = 0; foreach ($modelsItem as $index => $modelItem): ?>
        <?php
        $modelGdJadi = $modelItem->stock;
        $wo = $modelGdJadi->wo;
        $mo = $wo->mo;
        $scGreige = $mo->scGreige;
        $noLot = '';

        $panjangYds = 0;
        if($wo->greige->group->unitName === "Meter"){
            $panjangYds = \backend\components\Converter::meterToYard($modelGdJadi->qty);
            $totalPanjangYds += $panjangYds;
        }

        $totalPanjang += $modelGdJadi->qty;

        //mengambil no_lot
        if($modelGdJadi->source_ref !== null){
            $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                ->select('no_lot')
                ->where(['no'=>$modelGdJadi->source_ref])
                ->one()
            ;
            if($noLot !== false){
                $noLot = $noLot['no_lot'];
            }else{
                $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                    ->select('no_lot')
                    ->where(['no'=>$modelGdJadi->source_ref])
                    ->one()
                ;
                if($noLot !== false){
                    $noLot = $noLot['no_lot'];
                }
            }
        }
        ?>
        <tr>
            <td><span><?= ($index + 1) ?></span></td>
            <td><?=TrnScGreige::processOptions()[$scGreige->process]?> <?=$scGreige->greigeGroup->lebarKainName?>"</td>
            <td><?=$wo->greigeNamaKain?> <?=$mo->article?></td>
            <td><?=Yii::$app->formatter->asDecimal($modelGdJadi->qty)?> <?=$wo->greige->group->unitName?></td>
            <td><?=Yii::$app->formatter->asDecimal($panjangYds)?> YDS</td>
            <td><?=$modelItem->note?></td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="3"><strong>TOTAL</strong></td>
        <td><strong><?=Yii::$app->formatter->asDecimal($totalPanjang)?></strong></td>
        <td><strong><?=Yii::$app->formatter->asDecimal($totalPanjangYds)?></strong></td>
        <td></td>
    </tr>
    </tbody>
</table>

<div class="row">
    <div class="col-xs-3 text-center">
        Penerima<br><br><br>
        (<?=$model->penerima?>)
    </div>

    <div class="col-xs-3 text-center">
        Ka. Gudang<br><br><br>
        (<?=$model->kepala_gudang?>)
    </div>

    <div class="col-xs-3 text-center">
        Hormat Kami, <br><br><br>
        (<?=$model->pengirim?>)
    </div>

    <div class="col-xs-3">
        Catatan<br>
        <div style="border: 1px solid black; padding: 3px;"><?=$model->note?></div>
    </div>
</div>