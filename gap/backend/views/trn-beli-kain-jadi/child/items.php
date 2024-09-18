<?php

use common\models\ar\TrnBeliKainJadi;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnBeliKainJadi */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnBeliKainJadiItems(),
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
            'id' => 'KartuProsesMaklonItemsGrid',
            'pjax' => true,
            'responsiveWrap' => false,
            'resizableColumns' => false,
            'showPageSummary' => true,
            'toolbar' => false,
            'panel' => false,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                [
                    'attribute'=>'qty',
                    'format'=>'decimal',
                    'pageSummary' => true,
                    'hAlign' => 'right'
                ],
                'note:ntext',
            ],
        ])?>
    </div>
</div>
