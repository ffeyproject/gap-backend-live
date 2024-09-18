<?php
use common\models\ar\MstHandling;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstHandlingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Data Handling';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-handling-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            [
                'attribute' => 'greigeNamaKain',
                'label' => 'Greige',
                'value' => function($data){
                    /* @var $data MstHandling*/
                    return Html::a($data->greige->nama_kain, ['/mst-greige/view', 'id'=>$data->greige_id], ['target'=>'blank', 'title'=>'Detail Greige']);
                },
                'format'=>'raw'
            ],
            'name',
            'lebar_preset',
            'lebar_finish',
            //'berat_finish',
            //'densiti_lusi',
            //'densiti_pakan',
            'no_hanger',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
