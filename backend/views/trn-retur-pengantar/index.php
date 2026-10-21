<?php

use common\models\ar\TrnReturPengantar;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnReturPengantarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Surat Pengantar Retur';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-retur-pengantar-index">
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
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'date:date',
            'no',
            [
                'attribute' => 'bagian',
                'value' => 'bagianName',
                'filter' => TrnReturPengantar::bagianOptions(),
            ],
            'penerima',
            [
                'label' => 'Roll',
                'value' => function($data){
                    /* @var $data TrnReturPengantar*/
                    return $data->getTrnReturPengantarItems()->count('id');
                },
                'format' => 'decimal',
            ],
            [
                'label' => 'Qty',
                'value' => function($data){
                    /* @var $data TrnReturPengantar*/
                    return $data->getTrnReturPengantarItems()->sum('qty');
                },
                'format' => 'decimal',
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnReturPengantar*/
                    return TrnReturPengantar::statusOptions()[$data->status];
                },
                'filter' => TrnReturPengantar::statusOptions(),
            ],
            'note:ntext',
            'created_at:datetime',
        ],
    ]); ?>
</div>
