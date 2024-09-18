<?php

use common\models\ar\JualExFinish;
use common\models\ar\MstCustomer;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\JualExFinishSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Jual Ex Finishes';
$this->params['breadcrumbs'][] = $this->title;

$customerNameFilter = '';
if(!empty($searchModel->customer_id)){
    $customerNameFilter = MstCustomer::findOne($searchModel['customer_id'])->name;
}
?>
<div class="jual-ex-finish-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            'no_urut',
            'no',
            [
                'attribute'=>'jenis_gudang',
                'value'=>function($data){
                    /* @var $data JualExFinish*/
                    return $data->jenisGudangName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => JualExFinish::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'no_po',
            [
                'attribute'=>'customer_id',
                'value'=>'customer.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $customerNameFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari customer...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/customer-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            //'grade',
            [
                'attribute' => 'grade',
                'value' => function ($data){
                    /* @var $data JualExFinish*/
                    return $data->gradeName;
                },
                'filter' => TrnStockGreige::gradeOptions()
            ],
            'harga:decimal',
            [
                'attribute' => 'ongkir',
                'value' => function ($data){
                    /* @var $data JualExFinish*/
                    return $data->ongkirName;
                },
                'filter' => TrnSc::ongkosAngkutOptions()
            ],
            //'pembayaran',
            //'tanggal_pengiriman:date',
            //'komisi',
            //'jenis_order',
            [
                'attribute' => 'jenis_order',
                'value' => function ($data){
                    /* @var $data JualExFinish*/
                    return $data->jenisOrderName;
                },
                'filter' => \common\models\ar\TrnScGreige::processOptions()
            ],
            //'keterangan:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
