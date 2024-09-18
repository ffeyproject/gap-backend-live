<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\SuratJalanExFinishSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Surat Jalan Ex Finishes';
$this->params['breadcrumbs'][] = $this->title;

$noMemoFilter = '';
if(!empty($searchModel->memo_id)){
    $noMemoFilter = \common\models\ar\JualExFinish::findOne($searchModel['memo_id'])->no;
}
?>
<div class="surat-jalan-ex-finish-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            //'memo_id',
            [
                'attribute'=>'memo_id',
                'label' => 'No. Memo Penjualan',
                'value'=>'memo.no',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $noMemoFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari memo...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/jual-ex-finish-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            'no',
            'pengirim',
            'penerima',
            'kepala_gudang',
            //'note:ntext',
            'created_at:datetime',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
