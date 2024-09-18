<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\PfpKeluarVerpacking;
use common\models\ar\TrnPfpKeluar;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\PfpKeluarVerpackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pfp Keluar Verpackings';
$this->params['breadcrumbs'][] = $this->title;

$greigeNameFilter = '';
if(!empty($searchModel->greige_id)){
    $greigeNameFilter = \common\models\ar\MstGreige::findOne($searchModel['greige_id'])->nama_kain;
}
?>
<div class="pfp-keluar-verpacking-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            [
                'attribute'=>'greige_id',
                'value'=>'greigeNamaKain',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $greigeNameFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
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
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            //'pfp_keluar_id',
            [
                'label' => 'No. PFP Keluar',
                'attribute' => 'pfpKeluarNo',
                'value' => 'pfpKeluar.no',
            ],
            //'no_urut',
            'no',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data PfpKeluarVerpacking*/
                    return PfpKeluarVerpacking::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => PfpKeluarVerpacking::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'jenis',
                'value'=>function($data){
                    /* @var $data PfpKeluarVerpacking*/
                    return TrnPfpKeluar::jenisOptions()[$data->jenis];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnPfpKeluar::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'satuan',
                'value'=>function($data){
                    /* @var $data PfpKeluarVerpacking*/
                    return MstGreigeGroup::unitOptions()[$data->satuan];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'dateRangeKirim',
                'label' => 'Tgl. Kirim',
                'value' => 'tgl_kirim',
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
                'attribute' => 'dateRangeInspect',
                'label' => 'Tgl. Inspect',
                'value' => 'tgl_inspect',
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
            //'note:ntext',
            'send_to_vendor:boolean',
            //'vendor_id',
            //'wo_id',
            //'vendor_address:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
