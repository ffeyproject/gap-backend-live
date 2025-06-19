<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use kartik\widgets\Alert;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKirimBuyerBalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Raw Data Kirim Buyer Bal';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-bal-index">

    <?= Alert::widget([
        'options' => [
            //'class' => 'alert-info',
        ],
        'body' => '<b>Data Bal Kirim Buyer</b> ditampilkan berdasarkan nomor bal dan header ID.',
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'id'           => 'KirimBuyerBalGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'toolbar' => [
            '{toggleData}'
        ],
        'panel' => [
            'type'  => 'default',
            'before'=> Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            // 'after' => Html::a('Tambah Bal', ['create'], ['class' => 'btn btn-success']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn'],

            [
                'attribute' => 'id',
                'label'     => 'ID',
            ],
            [
                'attribute' => 'header_id',
                'value'     => 'header_id',
                'label'     => 'Header ID',
            ],
            [
                'attribute' => 'trn_kirim_buyer_id',
                'value'     => 'trnKirimBuyer.id',
                'label'     => 'Kirim Buyer ID',
            ],
            [
                'attribute' => 'no_bal',
                'label'     => 'No. Bal',
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<JS
console.log('KirimBuyerBal index ready');
JS;

$this->registerJs($js, View::POS_END);