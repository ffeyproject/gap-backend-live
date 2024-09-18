<?php
use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */

?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>MARKETING ORDER</h4>
        <strong>NO: <?=$model->no?></strong>
    </div>
</div>

<br>

<div class="row">
    <div class="col-xs-6">
        <?= DetailView::widget([
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-right:5px;'
            ],
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Jenis Proses',
                    'value' => $scGreige::processOptions()[$scGreige->process]
                ],
                [
                    'label' => 'Detail',
                    'value' => $sc::jenisOrderOptions()[$sc->jenis_order]
                ],
                'design',
                [
                    'label' => 'Lebar Finish',
                    'value' => $scGreige::lebarKainOptions()[$scGreige->lebar_kain].'"'
                ],
                'heat_cut:boolean',
                'piece_length',
                'strike_off:html',
                're_wo',
                /* Call Persentase Grading*/
                [
                    'label' => 'Persen Grading Pengkartuan Greige A/B',
                    'attribute' => 'persen_grading',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asPercent($model->persen_grading / 100);
                    },
                ],
            ],
        ]) ?>
    </div>

    <div class="col-xs-6">
        <?= DetailView::widget([
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-left:5px;'
            ],
            'model' => $model,
            'attributes' => [
                'date:date',
                [
                    'label' => 'Orientasi',
                    'value' => $sc::tipeKontrakOptions()[$sc->tipe_kontrak]
                ],
                [
                    'label' => 'Buyer',
                    'value' => $sc->cust->name
                ],
                [
                    'label' => 'No. Kontrak',
                    'value' => $sc->no
                ],
                [
                    'label' => 'Source Document',
                    'value' => $sc->no_po
                ],
                'est_produksi:date',
                'est_packing:date',
                'target_shipment:date'
            ],
        ]) ?>
    </div>
</div>

<?php echo $this->render('_colors', ['model' => $model, 'sc'=>$sc, 'scGreige'=>$scGreige]);?>

<br>

<strong>Accessories:</strong>

<div class="row">
    <div class="col-xs-6">
        <?= DetailView::widget([
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-right:5px;'
            ],
            'model' => $model,
            'attributes' => [
                'selvedge_stamping',
                'selvedge_continues',
                'side_band',
                'tag',
                'hanger',
                'label',
                'folder',
                'jet_black:boolean',
            ],
        ]) ?>
    </div>

    <div class="col-xs-6">
        <?= DetailView::widget([
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-left:5px;'
            ],
            'model' => $model,
            'attributes' => [
                'album',
                [
                    'label' => 'Joint',
                    'value' => $model->joint == 1 ? 'Ya, Max: '.$model->joint_qty : 'Tidak'
                ],
                [
                    'attribute'=>'packing_method',
                    'value'=>$model::packingMethodOptions()[$model->packing_method]
                ],
                [
                    'attribute'=>'shipping_method',
                    'value'=>$model::shippingMethodOptions()[$model->shipping_method]
                ],
                [
                    'attribute'=>'shipping_sorting',
                    'value'=>$model::shippingSortingOptions()[$model->shipping_sorting]
                ],
                [
                    'attribute'=>'plastic',
                    'value'=>$model::plasticOptions()[$model->plastic]
                ],
                'arsip',
                [
                    'label' => 'Shipping Sample',
                    'value' => $model->qtyShippingSample
                ]
            ],
        ]) ?>
    </div>
</div>

<p>
    <?= DetailView::widget([
        'options' => [
            'class' => 'table table-bordered small',
            'style' => 'margin-right:5px;'
        ],
        'model' => $model,
        'attributes' => [
            'face_stamping:html',
        ],
    ]) ?>
</p>

<strong>NOTE</strong>
<?php echo  $model->note;?>

<br>

<table class="table">
    <tr>
        <td style="width: 50%" class="text-center">
            Marketing<br>
            <?= Html::img($sc->marketing->signatureUrl, ['style'=>'height:100px;'])?><br>
            <?=$sc->marketingName?>
        </td>
        <td class="text-center">
            Mengetahui<br>
            <?= Html::img($model->approval->signatureUrl, ['style'=>'height:100px;'])?><br>
            <?=$model->approvalName?>
        </td>
    </tr>
</table>