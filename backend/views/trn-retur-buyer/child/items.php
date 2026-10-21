<?php

use common\models\ar\TrnReturBuyer;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnReturBuyer */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnReturBuyerItems()->with(['kirimBuyer.header', 'stock']),
    'pagination' => false,
    'sort' => false
]);
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">ITEMS</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body">
        <?=GridView::widget([
            'dataProvider' => $dataProvider,
            //'id' => 'KartuProsesMaklonItemsGrid',
            //'pjax' => true,
            'responsiveWrap' => false,
            'resizableColumns' => false,
            'showPageSummary' => true,
            'toolbar' => false,
            'panel' => false,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                [
                    'attribute'=>'kirim_buyer_id',
                    'label'=>'Surat Jalan',
                    'format'=>'raw',
                    'value'=>function($data){
                        /* @var $data \common\models\ar\TrnReturBuyerItem */
                        if($data->kirimBuyer === null){
                            return '<span class="text-muted">-</span>';
                        }
                        $header = $data->kirimBuyer->header;
                        return Html::a(Html::encode($header->no), ['/trn-kirim-buyer-header/view', 'id'=>$header->id], ['target'=>'_blank']);
                    }
                ],
                [
                    'attribute'=>'stock_id',
                    'label'=>'Roll Asal',
                    'format'=>'raw',
                    'value'=>function($data){
                        /* @var $data \common\models\ar\TrnReturBuyerItem */
                        if($data->stock === null){
                            return '<span class="label label-warning">Tidak diketahui</span>';
                        }
                        $text = '#'.$data->stock_id.($data->stock->qr_code ? ' | '.$data->stock->qr_code : '');
                        return Html::a(Html::encode($text), ['/trn-gudang-jadi/view', 'id'=>$data->stock_id], ['target'=>'_blank']);
                    }
                ],
                [
                    'attribute'=>'gudang_jadi_id',
                    'label'=>'Stok Hasil Retur',
                    'format'=>'raw',
                    'value'=>function($data){
                        /* @var $data \common\models\ar\TrnReturBuyerItem */
                        if(empty($data->gudang_jadi_id)){
                            return '<span class="text-muted">-</span>';
                        }
                        return Html::a('#'.$data->gudang_jadi_id, ['/trn-gudang-jadi/view', 'id'=>$data->gudang_jadi_id], ['target'=>'_blank']);
                    }
                ],
                [
                    'attribute'=>'qty',
                    'format'=>'decimal',
                    'pageSummary' => true,
                    'hAlign' => 'right'
                ],
                [
                    'attribute'=>'grade',
                    'value'=>function($data){
                        return \common\models\ar\TrnStockGreige::gradeOptions()[$data->grade];
                    }
                ],
                'note:ntext',
            ],
        ])?>
    </div>
</div>
