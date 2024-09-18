<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnMixedGreigeItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Mix Quality';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mixed-greige-item-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>'',
            //'footer'=>false
        ],
        //'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'mix_id',
            [
                'attribute' => 'grigeName',
                'value' => 'mix.greige.nama_kain'
            ],
            'stock_greige_id',

            //['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
