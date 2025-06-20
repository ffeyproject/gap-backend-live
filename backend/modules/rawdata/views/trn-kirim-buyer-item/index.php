<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use kartik\widgets\Alert;
use backend\modules\rawdata\models\TrnKirimBuyerItem;
use common\models\ar\TrnGudangJadi;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKirimBuyerItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Raw Data Kirim Buyer Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-item-index">
    <?=Alert::widget([
        'options' => [
            //'class' => 'alert-info',
        ],
        'body' => $this->render('ilustrasi/pergerakan_stock'),
    ])?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'KirimBuyerItemGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'toolbar' => [
            '{toggleData}'
        ],
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            // 'after' => Html::a('Tambah Item', ['create'], ['class' => 'btn btn-success']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn'],
            [
                'attribute' => 'id',
                'label' => 'ID',
            ],

            [
                'attribute' => 'header_id',
                'value' => 'kirimBuyer.header_id',
                'label' => 'Header ID',
            ],

            [
                'attribute' => 'kirim_buyer_id',
                'value' => 'kirimBuyer.id',
                'label' => 'Kirim Buyer ID',
            ],
            [
                'attribute' => 'stock_id',
                'value' => function($data){
                    return $data->stock ? $data->stock->id : null;
                },
                'label' => 'Stock ID',
            ],
            [
                'attribute' => 'noLot',
                'label' => 'No. Lot',
                'value' => function ($model) {
                    return $model->noLot;
                },
            ],

            'qty:decimal',
            'no_bal',
            'note:ntext',
            'bal_id',
        ],
    ]); ?>
</div>

<?php
$js = <<<JS
console.log('KirimBuyerItem index ready');
JS;

$this->registerJs($js, View::POS_END);