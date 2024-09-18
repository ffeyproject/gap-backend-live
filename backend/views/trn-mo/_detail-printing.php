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
            'design',
            'border_size',
            'block_size',
            'foil:boolean',
            'strike_off:html'
        ],
    ]) ?>
</p>