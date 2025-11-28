<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = "Riwayat Kartu #{$model->id} - {$model->no}";
$this->params['breadcrumbs'][] = ['label' => 'Data Kartu', 'url' => ['data-kartu-proses-dyeing']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
/* === Modern Card Styling === */
.history-card {
    border-radius: 12px;
    padding: 20px;
    background: #ffffff;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.09);
    margin-bottom: 25px;
}

/* === Timeline Styling === */
.timeline {
    position: relative;
    margin-left: 20px;
}

.timeline:before {
    content: "";
    position: absolute;
    left: 15px;
    width: 3px;
    top: 0;
    bottom: 0;
    background: #d7d7d7;
    border-radius: 3px;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 10px;
}

.timeline-bullet {
    width: 15px;
    height: 15px;
    background: #17a2b8;
    border-radius: 50%;
    position: absolute;
    left: 8px;
    top: 5px;
    box-shadow: 0 0 0 4px rgba(23, 162, 184, 0.3);
}

.timeline-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 3px;
}

.timeline-description {
    color: #555;
}

.timeline-meta {
    font-size: 13px;
    color: #777;
}

/* Badge Colors */
.badge-action {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
}

.badge-start {
    background: #28a745;
    color: white;
}

.badge-warning2 {
    background: #ffc107;
    color: black;
}

.badge-danger2 {
    background: #dc3545;
    color: white;
}

.badge-info2 {
    background: #17a2b8;
    color: white;
}

.badge-purple {
    background: #6f42c1;
    color: white;
}
</style>

<div class="history-card">
    <h3><i class="glyphicon glyphicon-time"></i> Riwayat Aktivitas Kartu di Verpacking</h3>
    <hr>

    <?php if (empty($logs)): ?>
    <p><em>Tidak ada riwayat untuk kartu ini.</em></p>
    <?php else: ?>

    <div class="timeline">

        <?php foreach ($logs as $log): ?>

        <div class="timeline-item">

            <div class="timeline-bullet"></div>

            <div class="timeline-title">
                <?= Html::encode($log->action_name) ?>
            </div>

            <div class="timeline-description">
                <?= !empty($log->description)
                            ? Html::encode($log->description)
                            : '<em>Tidak ada deskripsi</em>' ?>
            </div>

            <div class="timeline-meta">
                <i class="glyphicon glyphicon-user"></i>
                <?= Html::encode($log->username) ?>
                &nbsp; • &nbsp;
                <i class="glyphicon glyphicon-calendar"></i>
                <?= date('d M Y H:i', strtotime($log->created_at)) ?>
                &nbsp; • &nbsp;
                <i class="glyphicon glyphicon-globe"></i>
                <?= Html::encode($log->ip) ?>
            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>
</div>

<?= GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $logs,
        'pagination' => ['pageSize' => 15],
    ]),
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'attribute' => 'action_name',
            'label' => 'Aksi',
            'format' => 'raw',
            'value' => function($model) {
                return "<span class='badge-action badge-info2'>" .
                    Html::encode($model->action_name) .
                "</span>";
            }
        ],
        [
            'attribute' => 'description',
            'label' => 'Deskripsi',
        ],
        [
            'attribute' => 'username',
            'label' => 'User',
            'format' => 'raw',
            'value' => function ($m) {
                return '<b>' . Html::encode($m->username) . '</b>';
            },
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Waktu',
            'format' => 'raw',
            'value' => function($m) {
                return date('d F Y H:i', strtotime($m->created_at)); // tidak dilakukan konversi timezone
            }
        ],
    ],
    'panel' => [
        'heading' => "<b>Log Aktivitas (Tabel)</b>",
        'type' => 'info',
    ],
]); ?>