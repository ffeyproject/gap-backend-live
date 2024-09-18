<?php
use common\models\ar\TrnMo;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\DetailView;

/* @var $model common\models\ar\TrnMo */
/* @var $model TrnMo */
/* @var $formatter Formatter */
/* @var $detailView string */
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">MO Detail</h3>
        <div class="box-tools pull-right">
            <?=Html::a('<span class="glyphicon glyphicon-print" aria-hidden="true"></span>', ['print-mo', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-default',
                'title' => 'Print MO',
                'target' => 'blank'
            ])?>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'sc_greige_id',
                        'date:date',
                        'est_produksi:date',
                        'est_packing:date',
                        'target_shipment:date',
                        [
                            'label' => 'Piece Length',
                            'value' => $model->piece_length
                        ],
                        'jet_black:boolean',
                        'heat_cut:boolean',
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

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'article',
                        'hanger',
                        'label',
                        [
                            'label' => 'Joint',
                            'value' => function ($model, $widget) use($formatter){
                                /* @var $model TrnMo*/
                                $joint = $formatter->asBoolean($model->joint);
                                if($model->joint !== null){
                                    return $joint.', Max: '.$model->joint_qty;
                                }

                                return $joint;
                            }
                        ],
                        'selvedge_stamping',
                        'selvedge_continues',
                        'side_band',
                        'tag',
                    ],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'folder',
                        'arsip',
                        'album',
                        [
                            'attribute' => 'packing_method',
                            'value' => $model::packingMethodOptions()[$model->packing_method]
                        ],
                        [
                            'attribute' => 'shipping_method',
                            'value' => $model::shippingMethodOptions()[$model->shipping_method]
                        ],
                        [
                            'attribute' => 'shipping_sorting',
                            'value' => $model::shippingSortingOptions()[$model->shipping_sorting]
                        ],
                        [
                            'attribute' => 'plastic',
                            'value' => $model::plasticOptions()[$model->plastic]
                        ],
                        [
                            'attribute' => 'jenis_gudang',
                            'value' => TrnStockGreige::jenisGudangOptions()[$model->jenis_gudang]
                        ],
                    ],
                ]) ?>
            </div>
        </div>

        <?=$detailView?>

        <p>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'handling',
                    'face_stamping:html',
                    'note:html',
                ],
            ]) ?>
        </p>
    </div>
</div>
