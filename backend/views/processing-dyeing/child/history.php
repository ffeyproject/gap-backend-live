<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$logs = $model->actionLogs; // Relasi getActionLogs()

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
                'heading' => '<strong><i class="glyphicon glyphicon-time"></i> Riwayat Perubahan & Aktivitas Kartu Proses</strong>',
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
                        return date('d-m-Y H:i:s', strtotime($data->created_at));
                    }
                ],
                [
                    'attribute' => 'username',
                    'label' => 'User',
                    'format' => 'raw',
                    'value' => function($data) {
                        return '<strong>' . Html::encode($data->username) . '</strong>';
                    }
                ],
                [
                    'attribute' => 'action_name',
                    'label' => 'Aksi',
                    'format' => 'raw',
                    'value' => function($data) {
                        $labelClass = 'label label-default';
                        $labelText = Html::encode($data->action_name);
                        
                        switch ($data->action_name) {
                            case 'gabung':
                                $labelClass = 'label label-success';
                                $labelText = 'Gabung Kartu (Baru)';
                                break;
                            case 'gabung_source':
                                $labelClass = 'label label-warning';
                                $labelText = 'Gabung Kartu (Sumber)';
                                break;
                            case 'ganti_wo':
                                $labelClass = 'label label-warning';
                                $labelText = 'Ganti WO';
                                break;
                            case 'ganti_warna':
                                $labelClass = 'label label-info';
                                $labelText = 'Ganti Warna';
                                break;
                            case 'masuk_verpacking':
                                $labelClass = 'label label-success';
                                $labelText = 'Masuk Verpacking';
                                break;
                            case 'close_kartu':
                                $labelClass = 'label label-danger';
                                $labelText = 'Close Kartu';
                                break;
                            case 'rolling_packing':
                                $labelClass = 'label label-primary';
                                $labelText = 'Rolling Packing';
                                break;
                            case 'make_up_packing':
                                $labelClass = 'label label-success';
                                $labelText = 'Make Up Packing';
                                break;
                            case 'folded_packing':
                                $labelClass = 'label label-info';
                                $labelText = 'Folded Packing';
                                break;
                            case 'selvedge_packing':
                                $labelClass = 'label label-warning';
                                $labelText = 'Selvedge Packing';
                                break;
                        }
                        return "<span class='{$labelClass}'>{$labelText}</span>";
                    }
                ],
                [
                    'attribute' => 'description',
                    'label' => 'Keterangan / Deskripsi Perubahan',
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'ip',
                    'label' => 'IP Address',
                ],
            ],
        ]) ?>
    </div>
</div>
