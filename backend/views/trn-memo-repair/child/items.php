<?php

use common\models\ar\TrnReturBuyer;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model TrnReturBuyer */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnReturBuyerItems(),
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
                    'attribute'=>'grade',
                    'value'=>function($data){
                        return \common\models\ar\TrnStockGreige::gradeOptions()[$data->grade];
                    }
                ],
                [
                    'attribute'=>'qty',
                    'format'=>'decimal',
                    'pageSummary' => true,
                    'hAlign' => 'right'
                ],
                [
                    'label'=>'Unit',
                    'value' =>function($data) use($model){
                        return \common\models\ar\MstGreigeGroup::unitOptions()[$model->unit];
                    }
                ],
                'note:ntext',
            ],
        ])?>
    </div>
</div>
