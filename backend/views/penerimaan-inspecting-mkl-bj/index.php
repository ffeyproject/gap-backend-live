<?php
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel InspectingMklBjSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Packing Makloon Dan Barang Jadi';
$this->params['breadcrumbs'][] = $this->title;

$woNoFilter = '';
if(!empty($searchModel->wo_id)){
    $woNoFilter = \common\models\ar\TrnWo::findOne($searchModel['wo_id'])->no;
}
?>
<div class="inspecting-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            'no',
            [
                'attribute'=>'wo_id',
                'label'=>'WO No.',
                'value'=>'woNo',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $woNoFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/ajax/lookup-wo-all']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            'greigeName',
            'colorName',
            'designName',
            'articleName',
            'tgl_inspeksi:date',
            'tgl_kirim:date',
            'no_lot',
            [
                'attribute'=>'satuan',
                'value'=>'satuanName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label' => 'Total Qty',
                'value'=>function($data){
                    /* @var $data InspectingMklBj */
                    $total = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->sum('qty')
                    ;
                    return $total > 0 ? $total : 0;
                },
                'format' => 'decimal'
            ],
            //'jenis',
            [
                'attribute'=>'jenis',
                'value'=>'jenisName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            /*[
                'attribute'=>'status',
                'value'=>'statusName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
        ],
    ]); ?>


</div>
