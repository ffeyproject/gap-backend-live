<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use common\models\rekap\RekapWoTotalSearch;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel RekapWoTotalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form ActiveForm */

$this->title = 'Rekap Jumlah Working Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-index">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Filter</strong></div>

        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'action' => ['rekap-total-wo'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?=$form->field($searchModel, 'dateRange')->widget(\kartik\daterange\DateRangePicker::className(), [
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            //'timePicker'=>true,
                            //'timePickerIncrement'=>5,
                            'locale'=>[
                                //'format'=>'Y-m-d H:i:s',
                                'format'=>'Y-m-d',
                                'separator'=>' to ',
                            ]
                        ]
                    ])->label('Tanggal')?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($searchModel, 'proccess')->widget(\kartik\widgets\Select2::class, [
                        'data' => TrnScGreige::processOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ])?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($searchModel, 'jenis_order')->widget(\kartik\widgets\Select2::class, [
                        'data' => TrnSc::jenisOrderOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ])?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?php echo Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-order-actual-dyeing'], ['class' => 'btn btn-default']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        //'floatHeader' => true,
        //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            //'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-order-actual-dyeing'], ['class' => 'btn btn-default']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            //'id',
            [
                'attribute' => 'no',
                'label' => 'Nomor WO',
            ],
            [
                'label' => 'Jumlah Greige',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->colorQtyBatchToMeter;
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label' => 'Finish (Meter)',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->colorQtyFinishToMeter;
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label' => 'Finish (Yard)',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->colorQtyFinishToYard;
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            /*[
                'class' => '\kartik\grid\FormulaColumn',
                'label'=>'Finish (Yard)',
                'value' => function ($model, $key, $index, $widget) {
                    $p = compact('model', 'key', 'index');
                    // Write your formula below
                    return $widget->col(3, $p);
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],*/
        ],
    ]); ?>
</div>
