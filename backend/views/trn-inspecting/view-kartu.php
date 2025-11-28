<?php

use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$this->title = "Kartu Proses Dyeing : " . $model->no;
$this->params['breadcrumbs'][] = ['label' => 'Data Kartu Proses Dyeing Inspecting', 'url' => ['data-kartu-proses-dyeing']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
/* === CARD STYLE === */
.card-modern {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

.card-header-modern {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    border-bottom: 2px solid #f1f1f1;
    padding-bottom: 10px;
}

/* === BUTTON GROUP === */
.btn-action {
    margin-right: 8px;
    margin-bottom: 6px;
    font-weight: bold;
}

.btn-action i {
    margin-right: 4px;
}

/* === BADGE STATUS BESAR === */
.badge-status {
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 8px;
    font-weight: 600;
}


/* warna custom */
.badge-purple {
    background-color: #6f42c1;
    color: white;
}
</style>

<div class="card-modern">

    <div class="card-header-modern">
        <i class="glyphicon glyphicon-file"></i> Detail Kartu Proses Dyeing
    </div>

    <!-- BUTTON GROUP -->
    <div class="mb-3">

        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali',
            ['data-kartu-proses-dyeing'],
            ['class' => 'btn btn-default btn-action']
        ) ?>

        <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Rolling Packing',
            ['set-rolling-packing', 'id'=>$model->id],
            [
                'class'=>'btn btn-warning btn-action',
                'data-confirm'=>'Ubah status menjadi Rolling Packing?',
                'data-method'=>'post'
            ]
        ) ?>

        <?= Html::a('<i class="glyphicon glyphicon-compressed"></i> Folded Packing',
            ['set-folded-packing', 'id'=>$model->id],
            [
                'class'=>'btn btn-info btn-action',
                'data-confirm'=>'Ubah status menjadi Folded Packing?',
                'data-method'=>'post'
            ]
        ) ?>

        <?= Html::a('<i class="glyphicon glyphicon-tag"></i> Selvedge Packing',
            ['set-selvedge-packing', 'id'=>$model->id],
            [
                'class'=>'btn btn-warning btn-action',
                'data-confirm'=>'Ubah status menjadi Selvedge Packing?',
                'data-method'=>'post'
            ]
        ) ?>

        <?= Html::a('<i class="glyphicon glyphicon-check"></i> Make Up Packing',
            ['set-make-up-packing', 'id'=>$model->id],
            [
                'class'=>'btn btn-primary btn-action',
                'data-confirm'=>'Ubah status menjadi Make Up Packing?',
                'data-method'=>'post'
            ]
        ) ?>

        <?= Html::a('<i class="glyphicon glyphicon-lock"></i> Close Kartu',
            ['close-kartu', 'id'=>$model->id],
            [
                'class'=>'btn btn-danger btn-action',
                'data-confirm'=>'Yakin menutup kartu? Tindakan tidak dapat dibatalkan.',
                'data-method'=>'post'
            ]
        ) ?>

    </div>

    <!-- DETAIL VIEW STYLING -->
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-bordered detail-view'],
        'attributes' => [
            'id',

            [
                'label' => 'Nomor WO',
                'value' => $model->wo->no,
            ],

            'no',

            [
                'label' => 'Asal Greige',
                'value' => TrnStockGreige::asalGreigeOptions()[$model->asal_greige],
            ],

            'lusi',
            'pakan',

            [
                'label' => 'Warna',
                'value' => $model->woColor->moColor->color ?? '-',
            ],

            [
                'label' => 'Status Kartu',
                'format' => 'raw',
                'value' => function ($m) {
                    $label = TrnKartuProsesDyeing::statusOptionsFiltered()[$m->status] ?? '-';
                    $color = TrnKartuProsesDyeing::statusColor($m->status);

                    if ($color === 'purple') {
                        return "<span class='badge badge-status badge-purple'>{$label}</span>";
                    }

                    return "<span class='badge badge-status badge-{$color}'>{$label}</span>";
                }
            ],

            [
                'label' => 'Tanggal Kartu',
                'value' => Yii::$app->formatter->asDate($model->date, 'php:d F Y'),
            ],
        ],
    ]) ?>

</div>

<?php
// custom badge styling
$this->registerCss("
.badge-success { background:#28a745 !important; color:white; }
.badge-warning { background:#ffc107 !important; color:black; }
.badge-info    { background:#17a2b8 !important; color:white; }
.badge-danger  { background:#dc3545 !important; color:white; }
.badge-purple  { background:#6f42c1 !important; color:white; }
");
?>