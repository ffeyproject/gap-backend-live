<?php

use common\models\ar\TrnKirimBuyerHeader;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKirimBuyerHeaderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tab string */
/* @var $countLokal int */
/* @var $countExport int */

$isExport = ($tab === 'export');
$tabTitle = $isExport ? 'Pengiriman Export' : 'Pengiriman Lokal';
$this->title = 'Pengiriman Ke Buyer - ' . $tabTitle;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index', 'tab' => $tab]];
$this->params['breadcrumbs'][] = $tabTitle;
?>
<div class="trn-kirim-buyer-header-index">

    <div class="nav-tabs-custom" style="margin-bottom: 15px;">
        <ul class="nav nav-tabs">
            <li class="<?= !$isExport ? 'active' : '' ?>">
                <a href="<?= Url::to(['index', 'tab' => 'lokal']) ?>" style="font-weight: <?= !$isExport ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-truck text-green"></i> Pengiriman Lokal
                    <span class="label <?= !$isExport ? 'label-success' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($countLokal, 0, ',', '.') ?></span>
                </a>
            </li>
            <li class="<?= $isExport ? 'active' : '' ?>">
                <a href="<?= Url::to(['index', 'tab' => 'export']) ?>" style="font-weight: <?= $isExport ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-plane text-blue"></i> Pengiriman Export
                    <span class="label <?= $isExport ? 'label-primary' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($countExport, 0, ',', '.') ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content" style="padding: 10px 0 0 0;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsiveWrap' => false,
                'panel' => [
                    'type' => $isExport ? 'primary' : 'success',
                    'heading' => '<i class="' . ($isExport ? 'fa fa-plane' : 'fa fa-truck') . '"></i> ' . ($isExport ? 'Daftar Pengiriman Ke Buyer (Export)' : 'Daftar Pengiriman Ke Buyer (Lokal)'),
                    'before' => Html::tag(
                        'div',
                        Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index', 'tab' => $tab], ['class' => 'btn btn-default', 'title' => 'Refresh']) . ' ' .
                        ($isExport
                            ? Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Pengiriman Export', ['create', 'is_export' => 1], ['class' => 'btn btn-primary', 'title' => 'Buat Pengiriman Export Baru'])
                            : Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Pengiriman Lokal', ['create', 'is_export' => 0], ['class' => 'btn btn-success', 'title' => 'Buat Pengiriman Lokal Baru'])
                        ),
                        ['class' => 'btn-group', 'role' => 'group']
                    ),
                ],
                'toolbar' => false,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

                    [
                        'attribute' => 'no',
                        'label' => 'No. SJ',
                        'hAlign' => 'center',
                    ],
                    [
                        'label' => 'Buyer',
                        'attribute' => 'custName',
                        'value' => 'customer.name',
                    ],
                    [
                        'attribute' => 'nama_buyer',
                        'label' => 'Nama Buyer (SJ)',
                    ],
                    [
                        'attribute' => 'dateRange',
                        'label' => 'Tanggal',
                        'value' => 'date',
                        'format' => 'date',
                        'filterType' => GridView::FILTER_DATE_RANGE,
                        'filterWidgetOptions' => [
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'locale' => [
                                    'format' => 'Y-m-d',
                                    'separator' => ' to ',
                                ],
                            ],
                        ],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($data){
                            /* @var $data TrnKirimBuyerHeader */
                            return TrnKirimBuyerHeader::statusOptions()[$data->status];
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => TrnKirimBuyerHeader::statusOptions(),
                            'options' => ['placeholder' => '...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ],
                        'contentOptions' => function($data){
                            return ['class' => $data->status == TrnKirimBuyerHeader::STATUS_POSTED ? 'text-success' : 'text-warning', 'style' => 'font-weight: bold; text-align: center;'];
                        },
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'is_resmi',
                        'label' => 'Jenis SJ',
                        'visible' => !$isExport,
                        'format' => 'raw',
                        'value' => function($data){
                            return $data->is_resmi
                                ? '<span class="label label-primary">Resmi</span>'
                                : '<span class="label label-warning">Non Resmi</span>';
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [1 => 'Resmi', 0 => 'Non Resmi'],
                            'options' => ['placeholder' => '...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'hAlign' => 'center',
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
