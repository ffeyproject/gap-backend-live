<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="history-migrasi-wjl-index">
    <?php Pjax::begin(['id' => 'history-migrasi-pjax', 'enablePushState' => false]); ?>
    
    <form id="history-search-form" method="get" data-pjax="1" action="<?= \yii\helpers\Url::to(['history-migrasi-wjl']) ?>">
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div class="input-group">
                    <input type="text" name="search_motif" class="form-control" placeholder="Cari Nama Motif / Alias..." value="<?= Html::encode(Yii::$app->request->get('search_motif')) ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">
                            <i class="glyphicon glyphicon-search"></i> Cari
                        </button>
                        <?php if (Yii::$app->request->get('search_motif')): ?>
                            <?= Html::a('<i class="glyphicon glyphicon-remove"></i> Reset', ['history-migrasi-wjl'], ['class' => 'btn btn-default', 'data-pjax' => 1]) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </form>

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
