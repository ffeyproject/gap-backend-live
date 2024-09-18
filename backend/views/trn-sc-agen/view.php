<?php
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScAgen */
?>
<div class="trn-sc-agen-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sc_id',
            'date',
            'nama_agen',
            'attention',
            'no_urut',
            'no',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>

</div>
