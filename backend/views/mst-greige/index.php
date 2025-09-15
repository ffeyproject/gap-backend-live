<?php

use common\models\ar\MstGreige;
use kartik\widgets\Alert;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

$this->title = 'Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-index">

    <?= Alert::widget([
        'body' => $this->render('ilustrasi/pergerakan_stock'),
    ]) ?>

    <?php Pjax::begin(['id' => 'mst-greige-pjax']); ?>

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
                'attribute' => 'groupNamaKain',
                'label' => 'Greige Group',
                'value' => function($data){
                    return Html::a($data->group->nama_kain, ['/mst-greige-group/view', 'id'=>$data->group_id], ['target'=>'blank']);
                },
                'format'=>'raw'
            ],
            [
                'attribute' => 'nama_kain',
                'value' => function($data){
                    return Html::a($data->nama_kain, ['view', 'id'=>$data->id]);
                },
                'format'=>'raw'
            ],
            'alias',
            'no_dok_referensi',
            'gap:decimal',
            'aktif:boolean',
            'stock:decimal',
            [
                'attribute' => 'available',
                'value' => function($data){
                    return $data->available;
                },
                'format'=>'decimal'
            ],
            'booked_wo',
            'booked_opfp',
            'booked:decimal',
            'stock_pfp:decimal',
            'available_pfp:decimal',
            'booked_pfp:decimal',
            [
                'attribute' => 'status_weaving',
                'label' => 'Status Weaving',
                'value' => function($data) {
                    return $data->getStatusWeavingLabel();
                },
                'filter' => MstGreige::getStatusWeavingList(),
                'format' => 'raw',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update-weaving}',
                'buttons' => [
                    'update-weaving' => function($url, $model, $key){
                        return Html::a('<i class="glyphicon glyphicon-move"></i>', '#', [
                            'class' => 'btn btn-xs btn-primary',
                            'title' => 'Edit Status Weaving',
                            'onclick' => "
                                $('#modal-weaving').modal('show')
                                    .find('#modalContent')
                                    .load('".\yii\helpers\Url::to(['update-weaving', 'id' => $model->id])."');
                                return false;
                            ",
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
// Modal untuk edit status_weaving
Modal::begin([
    'id' => 'modal-weaving',
    'size' => 'modal-md',
]);

echo "<div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'>&times;</button>
        <h4 class='modal-title'>Edit Status Weaving</h4>
      </div>";

echo "<div id='modalContent'></div>";

Modal::end();
?>