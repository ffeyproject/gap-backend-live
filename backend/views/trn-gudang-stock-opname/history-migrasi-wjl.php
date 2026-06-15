<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="history-migrasi-wjl-index">
    <?php Pjax::begin(['id' => 'history-migrasi-pjax', 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'greige_id',
                'label' => 'Nama Motif',
                'value' => function($model) {
                    return $model->greige ? $model->greige->nama_kain : '-';
                }
            ],
            [
                'attribute' => 'total_qty_out',
                'label' => 'Total Qty di-OUT-kan',
                'format' => ['decimal', 2]
            ],
            [
                'attribute' => 'jumlah_roll_out',
                'label' => 'Jumlah Roll di-OUT-kan',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Waktu Migrasi',
                'format' => 'datetime'
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Diproses Oleh',
                'value' => function($model) {
                    return \common\models\User::findOne($model->created_by)->username ?? '-';
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
