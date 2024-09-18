<?php
use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnKartuProsesMaklonItem;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesMaklon */

$wo = $model->wo;
$greige = $wo->greige;
$greigeGroup = $greige->group;

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesMaklonItems(),
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
        <p><?='<strong>Greige: '.$greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($greigeGroup->qty_per_batch).' '.$greigeGroup::unitOptions()[$greigeGroup->unit].'</strong>'?></p>

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
                //'status',
                //'created_at',
                //'created_by',
                //'updated_at',
                //'updated_by',
            ],
        ])?>
    </div>
</div>
