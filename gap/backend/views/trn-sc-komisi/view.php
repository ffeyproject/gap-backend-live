<?php
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScKomisi */
?>
<div class="trn-sc-komisi-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sc_id',
            'sc_agen_id',
            'sc_greige_id',
            'komisi_amount',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>
</div>
