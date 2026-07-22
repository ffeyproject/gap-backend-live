<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use common\models\ar\TrnKartuProsesPfpItemLog;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */

$logs = $model->pfpItemLogs; // Relasi getPfpItemLogs()

$dataProvider = new ArrayDataProvider([
    'allModels' => $logs,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => ['created_at'],
        'defaultOrder' => ['created_at' => SORT_DESC],
    ],
]);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'responsiveWrap' => false,
            'resizableColumns' => false,
            'toolbar' => false,
            'panel' => [
                'heading' => '<strong><i class="glyphicon glyphicon-time"></i> Riwayat Perubahan & Aktivitas Kartu Proses PFP (Pengurangan Qty Pcs)</strong>',
                'type' => GridView::TYPE_INFO,
                'before' => false,
                'after' => false,
                'footer' => false
            ],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                [
                    'attribute' => 'created_at',
                    'label' => 'Waktu',
                    'value' => function($data) {
                        return date('d-m-Y H:i:s', $data->created_at);
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'User',
                    'format' => 'raw',
                    'value' => function($data) {
                        return '<strong>' . Html::encode($data->createdBy->username ?? '-') . '</strong>';
                    }
                ],
                [
                    'attribute' => 'action_type',
                    'label' => 'Aksi',
                    'format' => 'raw',
                    'value' => function($data) {
                        $labelClass = 'label label-default';
                        $labelText = 'Unknown';
                        
                        switch ($data->action_type) {
                            case TrnKartuProsesPfpItemLog::ACTION_TAMBAH:
                                $labelClass = 'label label-success';
                                $labelText = 'Tambah Roll';
                                break;
                            case TrnKartuProsesPfpItemLog::ACTION_HAPUS:
                                $labelClass = 'label label-danger';
                                $labelText = 'Hapus Roll';
                                break;
                            case TrnKartuProsesPfpItemLog::ACTION_UBAH_QTY:
                                $labelClass = 'label label-warning';
                                $labelText = 'Ubah Qty Roll';
                                break;
                        }
                        return "<span class='{$labelClass}'>{$labelText}</span>";
                    }
                ],
                [
                    'label' => 'Stock ID (Barcode)',
                    'value' => function($data) {
                        return $data->stock_id ? $data->stock_id : '-';
                    }
                ],
                [
                    'attribute' => 'qty_before',
                    'label' => 'Qty Sebelum',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'qty_after',
                    'label' => 'Qty Sesudah',
                    'format' => ['decimal', 2],
                ],
                [
                    'label' => 'Selisih',
                    'format' => ['decimal', 2],
                    'value' => function($data) {
                        return (float)$data->qty_before - (float)$data->qty_after;
                    }
                ],
                [
                    'attribute' => 'alasan',
                    'label' => 'Alasan / Keterangan',
                    'format' => 'ntext',
                ],
            ],
        ]) ?>
    </div>
</div>
