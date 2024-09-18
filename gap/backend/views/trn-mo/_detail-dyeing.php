<?php
use common\models\ar\TrnMo;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnMo */
?>

<p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sulam_pinggir',
            'no_lab_dip'
        ],
    ]) ?>
</p>