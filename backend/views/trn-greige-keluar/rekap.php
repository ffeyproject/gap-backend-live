<?php

use common\models\ar\TrnGreigeKeluar;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LaporanGreigeKeluar */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Greige Keluar';
$this->params['breadcrumbs'][] = $this->title;

$greigeLbl = '';
$greigeUnit = '';
if(!empty($searchModel->greigeId)){
    $greige = \common\models\ar\MstGreige::findOne($searchModel->greigeId);
    if($greige !== null){
        $greigeLbl = $greige->nama_kain;
        $greigeUnit = $greige->group->unitName;
    }
}
?>
<div class="trn-greige-keluar-index">
    <!--<div class="box">
        <div class="box-body">
            <blockquote>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
                <footer>Someone famous in <cite title="Source Title">Source Title</cite></footer>
            </blockquote>
        </div>
    </div>-->

    <?php // echo $this->render('_search_rekap', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
            //'no_urut',
            //'no',
            [
                'attribute'=>'no',
                'label'=>'No. Ref'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            [
                'label'=>'Nama Motif',
                'attribute'=>'greigeId',
                'value'=>function($data) use($searchModel){
                    /* @var $data TrnGreigeKeluar*/
                    return \common\models\ar\MstGreige::findOne($searchModel->greigeId)->nama_kain;
                    //return $searchModel->greigeId;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $greigeLbl, // set the initial display text
                    'options' => ['placeholder' => 'Cari Greige...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/lookup-greige']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                        'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                    ]
                ],
            ],
            [
                'label'=>'Qty 1',
                'value'=>function($data){
                    $modelsItem = $data->trnGreigeKeluarItems;
                    return count($modelsItem);
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Sat',
                'value'=>function($data){
                    return 'pcs';
                }
            ],
            [
                'label'=>'Qty 2',
                'value'=>function($data) use($searchModel){
                    /* @var $data TrnGreigeKeluar*/
                    $itemGids = $data->getTrnGreigeKeluarItems()->select(['stock_greige_id'])->asArray()->all();
                    $ids = \yii\helpers\ArrayHelper::getColumn($itemGids, 'stock_greige_id');
                    $total = \common\models\ar\TrnStockGreige::find()
                        ->where(['id'=>$ids])
                        ->andFilterWhere(['greige_id'=>$searchModel->greigeId])
                        ->sum('panjang_m')
                    ;
                    if($total > 0){
                        return $total;
                    }
                    return 0;
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Qty sat',
                'value'=>function($data) use($greigeUnit){
                    return $greigeUnit;
                }
            ],
            [
                'attribute'=>'jenis',
                'value'=>function($data){
                    /* @var $data TrnGreigeKeluar*/
                    return $data::jenisOptions()[$data->jenis];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGreigeKeluar::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'destinasi',
            'note',
        ],
    ]); ?>
</div>
