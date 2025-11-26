<?php

use common\models\ar\TrnKartuProsesDyeing;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$this->title = "Kartu Proses Dyeing : " . $model->no;
$this->params['breadcrumbs'][] = ['label' => 'Data Kartu Proses Dyeing Inspecting', 'url' => ['data-kartu-proses-dyeing']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-kartu-proses-dyeing-view">

    <h3><strong>Detail Kartu Proses Dyeing</strong></h3>
    <hr>

    <p>
        <?= Html::a('Kembali', ['data-kartu-proses-dyeing'], ['class' => 'btn btn-default']) ?>

        <?= Html::a(
        'Close Kartu Proses',
        ['close-kartu', 'id' => $model->id],
        [
            'class' => 'btn btn-danger ml-1',
            'data-confirm' => 'Yakin menutup kartu proses ini? Setelah close, data tidak dapat diproses ulang!',
            'data-method' => 'post'
        ]
        ) ?>

        <!-- BUTTON ROLLING PACKING -->
        <?= Html::a(
            'Set Rolling Packing',
            ['set-rolling-packing', 'id' => $model->id],
            [
                'class' => 'btn btn-warning ml-1',
                'data-confirm' => 'Yakin mengubah status menjadi Rolling Packing?',
                'data-method' => 'post'
            ]
        ) ?>

        <!-- BUTTON MAKE UP PACKING -->
        <?= Html::a(
            'Set Make Up Packing',
            ['set-make-up-packing', 'id' => $model->id],
            [
                'class' => 'btn btn-primary ml-1',
                'data-confirm' => 'Yakin mengubah status menjadi Make Up Packing?',
                'data-method' => 'post'
            ]
        ) ?>

        <!-- BUTTON FOLDED PACKING -->
        <?= Html::a(
            'Set Folded Packing',
            ['set-folded-packing', 'id' => $model->id],
            [
                'class' => 'btn btn-info ml-1',
                'data-confirm' => 'Yakin mengubah status menjadi Folded Packing?',
                'data-method' => 'post'
            ]
        ) ?>

        <!-- BUTTON SELVEDGE PACKING -->
        <?= Html::a(
            'Set Selvedge Packing',
            ['set-selvedge-packing', 'id' => $model->id],
            [
                'class' => 'btn btn-warning ml-1',
                'data-confirm' => 'Yakin mengubah status menjadi Selvedge Packing?',
                'data-method' => 'post'
            ]
        ) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Nomor WO',
                'value' => $model->wo->no,
            ],
            'no',
            [
                'label' => 'Asal Greige',
                'value' => \common\models\ar\TrnStockGreige::asalGreigeOptions()[$model->asal_greige],
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
                'value' => function($m){
                    $label = TrnKartuProsesDyeing::statusOptionsFiltered()[$m->status] ?? '-';
                    $color = TrnKartuProsesDyeing::statusColor($m->status);

                    if ($color === 'purple') {
                        return "<span class='badge badge-purple'>{$label}</span>";
                    }
                    return "<span class='badge badge-{$color}'>{$label}</span>";
                }
            ],
            [
                'label' => 'Tanggal Kartu',
                'value' => Yii::$app->formatter->asDate($model->date, 'php:d-m-Y'),
            ],
        ],
    ]) ?>

</div>

<?php
$this->registerCss("
.badge-success { background-color:#28a745; color:white; padding:5px 10px; border-radius:4px; }
.badge-warning { background-color:#ffc107; color:black; padding:5px 10px; border-radius:4px; }
.badge-purple  { background-color:#6f42c1; color:white; padding:5px 10px; border-radius:4px; }
");
?>