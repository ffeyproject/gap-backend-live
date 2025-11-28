<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = "Riwayat Kartu #{$model->id} - {$model->no}";
$this->params['breadcrumbs'][] = ['label' => 'Data Kartu', 'url' => ['data-kartu-proses-dyeing']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h3>Riwayat Aktivitas Kartu Proses Dyeing</h3>

<?= GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $logs,
        'pagination' => ['pageSize' => 20],
    ]),
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'attribute' => 'action_name',
            'label' => 'Aksi',
        ],
        [
            'attribute' => 'description',
            'label' => 'Deskripsi',
        ],
        [
            'attribute' => 'username',
            'label' => 'User',
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Waktu',
            'format' => 'datetime'
        ],
    ],
    'panel' => [
        'heading' => "<b>Riwayat Aksi</b>",
        'type' => 'info',
    ],
]); ?>