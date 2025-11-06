<?php
use kartik\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\ar\TrnWoColorSearch $searchModel */

$this->title = 'Rekap Tanggal Siap Warna';
$this->params['breadcrumbs'][] = ['label' => 'REKAP', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="rekap-ready-colour box box-solid">
    <div class="box-header with-border">
        <!-- <h3 class="box-title"><i class="glyphicon glyphicon-tint"></i> <?= Html::encode($this->title) ?></h3> -->
    </div>

    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel, // 🔹 aktifkan filter
            'hover' => true,
            'striped' => true,
            'condensed' => true,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<strong>Daftar Warna Siap Produksi</strong>',
            ],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                [
                    'attribute' => 'woNo',
                    'label' => 'WO No',
                    'format' => 'raw',
                    'value' => function($model) {
                        if ($model->wo && !empty($model->wo->no)) {
                            $url = \yii\helpers\Url::to(['/trn-wo/view', 'id' => $model->wo->id]);
                            return \yii\helpers\Html::a($model->wo->no, $url, [
                                'title' => 'Lihat detail WO ' . $model->wo->no,
                                'target' => '_blank',
                                'data-pjax' => '0',
                            ]);
                        }
                        return '-';
                    },
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Cari No WO...',
                    ],
                ],
                [
                    'attribute' => 'mo_color_id',
                    'label' => 'Color',
                    'value' => function($model) {
                        return $model->moColor->color ?? '-';
                    },
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari warna...'],
                ],
                [
                    'attribute' => 'greige_id',
                    'label' => 'Greige',
                    'value' => function($model) {
                        return $model->greige->nama_kain ?? '-';
                    },
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari greige...'],
                ],
                [
                    'attribute' => 'qty',
                    'label' => 'Qty (Batch)',
                    'format' => ['decimal', 2],
                    'hAlign' => 'right',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Qty...'],
                ],
               [
                    'attribute' => 'dateRangeReadyColour',
                    'label' => 'Tanggal Siap Warna',
                    'value' => function ($model) {
                        if (!empty($model->date_ready_colour)) {
                            return Yii::$app->formatter->asDate($model->date_ready_colour, 'php:d M Y');
                        }
                        return '-';
                    },
                    'format' => 'raw',
                    'filterType' => \kartik\grid\GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => ['format' => 'Y-m-d', 'separator' => ' to '],
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => 'Pilih tanggal...',
                        'style' => 'width:160px;', // 🔹 batasi lebar input di sini
                        'class' => 'form-control',
                    ],
                    'headerOptions' => ['style' => 'width:180px;'], // opsional untuk header kolom
                ],
                [
                    'attribute' => 'note',
                    'label' => 'Catatan',
                    'format' => 'ntext',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari catatan...'],
                ],
            ],
        ]) ?>
    </div>
</div>