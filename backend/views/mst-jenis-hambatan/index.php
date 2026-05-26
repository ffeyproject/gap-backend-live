<?php
use common\models\ar\MstJenisHambatan;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstJenisHambatanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Jenis Hambatan Mesin';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-jenis-hambatan-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']) .
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'toolbar' => [
            '{export}',
        ],
        'columns' => [
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view} {update} {delete}'],

            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'nama',
                'label' => 'Nama Hambatan',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data MstJenisHambatan */
                    return Html::a(Html::encode($data->nama), ['view', 'id' => $data->id]);
                },
            ],
            [
                'attribute' => 'keterangan',
                'label' => 'Keterangan',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Dibuat Pada',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Diperbarui Pada',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
            ],
        ],
    ]); ?>
</div>
