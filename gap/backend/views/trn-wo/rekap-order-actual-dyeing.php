<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnWoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form ActiveForm */

$this->title = 'Rekap Work Order Actual - Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-index">
    <?php $form = ActiveForm::begin([
        'action' => ['rekap-order-actual-dyeing'],
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
            <?=$form->field($searchModel, 'no')->textInput()?>
        </div>

        <div class="col-md-4"><?=$form->field($searchModel, 'scGreigeNamaKain')->textInput()->label('Motif')?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-order-actual-dyeing'], ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        //'floatHeader' => true,
        //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
        //'toolbar' => false,
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
                'label' => 'No. WO',
            ],
            [
                'label' => 'Motif',
                'value'=>'mo.scGreige.greigeGroup.nama_kain',
            ],
            [
                'label' => 'Qty Batch',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->colorQty;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Actual',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->getTrnKartuProsesDyeings()->count();
                    //return (new \yii\db\Query())->from('trn_kartu_proses_dyeing')->where(['wo_id'=>2456])->count();
                },
                'format' => 'decimal'
            ],
            [
                'class' => '\kartik\grid\FormulaColumn',
                'label'=>'Sisa',
                'value' => function ($model, $key, $index, $widget) {
                    $p = compact('model', 'key', 'index');
                    // Write your formula below
                    return $widget->col(3, $p) - $widget->col(4, $p);
                }
            ],
        ],
    ]); ?>
</div>
