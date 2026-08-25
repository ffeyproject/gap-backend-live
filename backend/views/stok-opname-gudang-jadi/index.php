<?php

use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TrnGudangJadiOpnamePcsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalPcs int */

$this->title = 'Stok Opname Gudang Jadi (trn_gudang_jadi_opname_pcs)';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stok-opname-gudang-jadi-index">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-barcode"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Item Pcs Opname</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalPcs) ?> Pcs / Roll</span>
                </div>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StokOpnameGdJadiGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'primary',
            'heading' => '<h3 class="panel-title"><i class="fa fa-list"></i> Data Stok Opname Gudang Jadi</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']),
            'after' => false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'label' => 'ID',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('<strong>#' . $model->id . '</strong>', ['view', 'id' => $model->id], [
                        'title' => 'Lihat Detail',
                        'data-pjax' => '0',
                    ]);
                }
            ],
            'opname_code',
            'qr_code',
            'qr_code_desc:ntext',
            [
                'attribute' => 'qty',
                'value' => function($model) {
                    return Yii::$app->formatter->asDecimal($model->qty);
                }
            ],
            'unit',
            [
                'attribute' => 'grade',
                'value' => function($model) {
                    return $model->gradeName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'join_piece',
            'locs_code',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->status === TrnGudangJadiOpnamePcs::STATUS_VERIFIED) {
                        return '<span class="label label-success">' . Html::encode($model->statusName) . '</span>';
                    }
                    return '<span class="label label-warning">' . Html::encode($model->statusName) . '</span>';
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadiOpnamePcs::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'id_trn_gudang_jadi',
            'remark:ntext',
            'created_at:datetime',

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], [
                            'title' => 'Detail Stok Opname Pcs',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    }
                ]
            ]
        ],
    ]); ?>
</div>
