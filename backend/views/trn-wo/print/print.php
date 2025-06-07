<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ar\TrnWo;
use common\models\ar\TrnSc;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $mo TrnMo */
/* @var $scGreige TrnScGreige */

$greigeGroup = $model->scGreige->greigeGroup;
$greige = $model->greige;
$unit = $greigeGroup::unitOptions()[$greigeGroup->unit];
$sc = $model->sc;
$colors = $model->trnWoColors;
$pieceLength = $mo->piece_length;
$qtyInUnit = $model->colorQtyBatchToMeter;

$heatCut = '';
if($mo->heat_cut == 1){
    $heatCut = '<strong>Heat Cut</strong>: Ya';
}else $heatCut = '<strong>Heat Cut</strong>: Tidak';
?>

<div class="row">
    <div class="col-xs-8">
        <?= DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'table table-bordered small',
            ],
            'attributes' => [
                [
                    'attribute' => 'date',
                    'value' => Yii::$app->formatter->asDate($model->date, 'php:d-m-Y')
                ],
                [
                    'label' => 'No. Mo',
                    'value' => $mo->no
                ],
                [
                    'label' => 'No. Wo',
                    'value' => $model->no
                ],
                [
                    'label' => 'Kode Pemesanan',
                    'value' => $sc->cust->cust_no
                ],
                [
                    'label' => 'Motif Sales Contract',
                    'value' => $scGreige->greigeGroup->nama_kain
                ],
                [
                    'label' => 'Motif Greige',
                    'value' => function($model) use($greige, $scGreige){
                        if($model->jenis_order === TrnSc::JENIS_ORDER_MAKLOON){
                            return $scGreige->greigeGroup->nama_kain;
                        }else{
                            return $greige->nama_kain;
                        }
                    }
                ],
                [
                    'label' => 'Artikel',
                    'value' => $mo->article
                ],
                [
                    'label' => 'Lebar Kain',
                    'format' => 'html',
                    'value' => $scGreige::lebarKainOptions()[$scGreige->lebar_kain].'", '.$heatCut
                ],
                [
                    'label' => 'Jumlah Greige',
                    'value' => Yii::$app->formatter->asDecimal($qtyInUnit).'M '
                ],
                [
                    'label' => 'ID SC',
                    'value' => $sc->id
                ],
                [
                    'label' => 'Target Shipment',
                    'value' => Yii::$app->formatter->asDate($mo->target_shipment, 'php:d-m-Y')
                ],
                [
                    'label' => 'Tujuan',
                    'value' => $sc->destination
                ],
                [
                    'label' => 'Kode Design',
                    'value' => $mo->design
                ],
                [
                    'label'=>'Handling',
                    'value'=>$model->handling->name
                ],
            ],
        ]) ?>

        <?=$this->render('colors', [
            'model'=>$model,
            'unitMeter'=>'M',
            'colors'=>$colors,
            'unit'=>$unit,
            'greige'=>$greige
        ])?>
    </div>

    <div class="col-xs-4" style="margin-left: 5px;">
        <div style="border: 1px solid #ddd; padding: 0 5px 0 5px;">
            Face Stamping:<br>
            <?=$mo->face_stamping?><br>

            Piece Length:<br>
            <?=$pieceLength?><br>
        </div>

        <br>

        <?=DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'table table-bordered small',
            ],
            'attributes' => [
                ['label' => 'Attribute', 'value' => 'Accessories'],
                ['label' => 'Side Band', 'value' => $mo->side_band],
                ['label' => 'Hang Tag', 'value' => $mo->tag],
                ['label' => 'Hanger', 'value' => $mo->hanger],
                ['label' => 'Label', 'value' => $mo->label],
                ['label' => 'Album', 'value' => $mo->album],
                ['label' => 'Folder', 'value' => $mo->folder],
                ['label' => 'Shiping Sample', 'value' => $mo->getQtyShippingSample()],
                ['label' => 'Arsip', 'value' => $mo->arsip],
                ['label' => 'Packing Method', 'value' => $mo::packingMethodOptions()[$mo->packing_method]],
                ['label' => 'Plastik', 'value' => $mo::plasticOptions()[$mo->plastic]],
                [
                    'label' => 'Plastic Type',
                    'value' => $model->plastic_size
                ],
                ['label' => 'Paper Tube', 'value' => $model->papperTube->name],
                ['label' => 'Shiping Shorting', 'value' => $mo::shippingSortingOptions()[$mo->shipping_sorting]],
            ]
        ])?>
    </div>
</div>

<p>Tanggal Kirim: <?=$model->tgl_kirim === null? '-' : Yii::$app->formatter->asDate($model->tgl_kirim)?></p>
<div class="row">
    <div class="col-xs-12" style="border: 1px solid #ddd; padding: 0 5px 0 5px;">
        Note:<br>
        <?= $model->note?>
    </div>
</div>

<p></p>

<div class="row">
    <div class="col-xs-12" style="border: 1px solid #ddd;">
        Shiping Mark:<br>
        <?= $model->shipping_mark?><br>

        Note Packing:<br>
        <?=$model->note_two?>
    </div>
</div>

<p></p>

<div class="row">
    <div class="col-xs-8">
        <div style="border: 1px solid #ddd; padding: 0 5px 0 5px;">
            Sulam Pinggir:<br><?=$mo->sulam_pinggir?>
            <br><br>
            Selvedge Stamping:<br><?=$mo->selvedge_stamping?>
            <br><br>
            Selvedge Continues:<br><?=$mo->selvedge_continues?><br><br>
            Repeat Order : <?=$mo->re_wo?>
            <br>
            Lampiran :
        </div>
    </div>

    <div class="col-xs-4" style="margin-left: 5px;">
        <div style="border: 1px solid #ddd; padding: 0 5px 0 5px;">
            Marketing:<br>
            <?= Html::img($sc->marketing->signatureUrl, ['style'=>'height:80px;'])?><br>
            <?=$model->marketingName?>
            <br><br>
            Mengetahui:<br>
            <?= Html::img($model->mengetahui->signatureUrl, ['style'=>'height:80px;'])?><br>
            <?=$model->mengetahuiName?>
        </div>
    </div>
</div>