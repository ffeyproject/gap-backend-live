<?php
use common\models\ar\MstKodeDefect;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstKodeDefectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Data Kode Defect';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-kode-defect-index">
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
            // Action column for view, update, and delete actions
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view} {update}'],

            // Columns from the MstKodeDefect model
            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'no_urut',
                'label' => 'No Urut',
            ],
            [
                'attribute' => 'kode',
                'label' => 'Kode',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data MstKodeDefect */
                    return Html::a($data->kode, ['view', 'id' => $data->id], [
                        'title' => 'Lihat Detail',
                        'target' => '_blank',
                    ]);
                },
            ],
            [
                'attribute' => 'nama_defect',
                'label' => 'Nama Defect',
            ],
            [
                'attribute' => 'asal_defect',
                'label' => 'Asal Defect',
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