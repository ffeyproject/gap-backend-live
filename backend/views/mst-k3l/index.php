<?php

use common\models\ar\MstK3l;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstK3lSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master K3L';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-k3l-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Master K3L', ['create'], ['class' => 'btn btn-success']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'toolbar' => [
            '{export}',
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {update}',
            ],
            [
                'attribute' => 'k3l_code',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var $model MstK3l */
                    return Html::a(Html::encode($model->k3l_code), ['view', 'id' => $model->k3l_code], ['title' => 'Lihat Detail']);
                },
            ],
            'k3l_desc',
            [
                'attribute' => 'k3l_active',
                'filter' => MstK3l::statusOptions(),
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var $model MstK3l */
                    if ($model->k3l_active === MstK3l::STATUS_AKTIF) {
                        return '<span class="label label-success">Aktif</span>';
                    }
                    return '<span class="label label-danger">Tidak Aktif</span>';
                },
                'headerOptions' => ['style' => 'width:120px; text-align:center;'],
                'contentOptions' => ['style' => 'text-align:center;'],
            ],
            [
                'attribute' => 'k3l_add_by',
                'label' => 'Dibuat Oleh',
                'value' => function ($model) {
                    /* @var $model MstK3l */
                    return $model->createdBy ? $model->createdBy->username : $model->k3l_add_by;
                },
            ],
            'k3l_add_date:datetime',
            [
                'attribute' => 'k3l_upd_by',
                'label' => 'Diubah Oleh',
                'value' => function ($model) {
                    /* @var $model MstK3l */
                    return $model->updatedBy ? $model->updatedBy->username : $model->k3l_upd_by;
                },
            ],
            'k3l_upd_date:datetime',
        ],
    ]); ?>
</div>
