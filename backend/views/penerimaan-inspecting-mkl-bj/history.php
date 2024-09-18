<?php
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use common\models\ar\InspectingMklBjItems;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel InspectingMklBjSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Penerimaan Packing Makloon Dan Barang Jadi';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider->pagination->pageSize = 10;

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
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['history'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
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
            'colorName',
            'tgl_inspeksi:date',
            'tgl_kirim:date',
            'no_lot',
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
            [
                'label' => 'Total Grade A',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_A])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade A+',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_A_PLUS])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade A*',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_A_ASTERISK])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade B',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_B])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade C',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_C])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Piece Kecil',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_PK])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Contoh',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingMklBjItems::GRADE_SAMPLE])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
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
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
        ],
    ]); ?>


</div>
