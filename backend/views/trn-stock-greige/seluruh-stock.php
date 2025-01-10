<?php
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $data array */
?>

<div class="seluruh-stock">
    <?= DetailView::widget([
        'model' => $data,
        'attributes' => [
            [
                'label' => 'Water Jet Loom',
                'value' => isset($data['water_jet_loom']) 
                    ? '['.implode(', ', array_map(function ($v, $k) {
                        return "[$k: $v]";
                    }, $data['water_jet_loom'], array_keys($data['water_jet_loom']))) . ']'
                    : 'Tidak ada data'
            ],
            [
                'label' => 'Rapier Loom',
                'value' => isset($data['rapier_loom']) 
                    ? '['.implode(', ', array_map(function ($v, $k) {
                        return "[$k: $v]";
                    }, $data['rapier_loom'], array_keys($data['rapier_loom']))).']'
                    : 'Tidak ada data'
            ],
            [
                'label' => 'Beli Lokal',
                'value' => isset($data['beli_lokal']) 
                    ? '['.implode(', ', array_map(function ($v, $k) {
                        return "[$k: $v]";
                    }, $data['beli_lokal'], array_keys($data['beli_lokal']))).']'
                    : 'Tidak ada data'
            ],
            [
                'label' => 'Beli Import',
                'value' => isset($data['beli_import']) 
                    ? '['.implode(', ', array_map(function ($v, $k) {
                        return "[$k: $v]";
                    }, $data['beli_import'], array_keys($data['beli_import']))).']'
                    : 'Tidak ada data'
            ],
        ],
    ]) ?>
</div>