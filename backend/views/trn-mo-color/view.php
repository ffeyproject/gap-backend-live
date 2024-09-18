<?php
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMoColor */
?>
<div class="trn-mo-color-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mo_id',
            'color',
            'qty:decimal',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>
</div>
