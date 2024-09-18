<?php
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $data array */
?>

<div class="seluruh-stock">
    <?= DetailView::widget([
        'model' => $data,
        'attributes' => [
            'water_jet_loom:decimal',
            'rapier_loom:decimal',
            'beli_lokal:decimal',
            'beli_import:decimal',
        ],
    ]) ?>
</div>
