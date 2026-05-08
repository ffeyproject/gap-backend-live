<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstMesinProcessingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mesin Processing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-mesin-processing-index">

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

            [
                'attribute' => 'nama_mesin',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a($data['nama_mesin'], ['view', 'id' => $data['id']]);
                }
            ],
            'jenis_mesin',
            'jenis_nozzle',
            'ukuran_nozzle',

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>

</div>
