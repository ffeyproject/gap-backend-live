<?php

use common\models\ar\MstGreige;
use kartik\widgets\Alert;
use yii\bootstrap\Collapse;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=Alert::widget([
        'options' => [
            //'class' => 'alert-info',
        ],
        'body' => $this->render('ilustrasi/pergerakan_stock'),
    ])?>


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
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            //'id',
            [
                'attribute' => 'groupNamaKain',
                'label' => 'Greige Group',
                'value' => function($data){
                    /* @var $data MstGreige*/
                    return Html::a($data->group->nama_kain, ['/mst-greige-group/view', 'id'=>$data->group_id], ['target'=>'blank', 'title'=>'Detail Greige Group']);
                },
                'format'=>'raw'
            ],
            [
                'attribute' => 'nama_kain',
                'value' => function($data){
                    /* @var $data MstGreige*/
                    return Html::a($data->nama_kain, ['view', 'id'=>$data->id], ['title'=>'Detail Greige']);
                },
                'format'=>'raw'
            ],
            'alias',
            'no_dok_referensi',
            'gap:decimal',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            'aktif:boolean',
            'stock:decimal',
            'available',
            'booked_wo',
            'booked:decimal',
            'stock_pfp:decimal',
            'booked_pfp:decimal'
        ],
    ]); ?>
</div>
