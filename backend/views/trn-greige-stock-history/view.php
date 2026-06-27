<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeStockHistory */

$this->title = 'History #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greige Stock History', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

function formatDetailChange($old, $new) {
    $oldVal = (float)$old;
    $newVal = (float)$new;
    if ($oldVal === $newVal) {
        return Yii::$app->formatter->asDecimal($oldVal);
    }
    $diff = $newVal - $oldVal;
    $class = $diff > 0 ? 'text-success' : 'text-danger';
    $sign = $diff > 0 ? '+' : '';
    $formattedDiff = Yii::$app->formatter->asDecimal($diff);
    
    return Yii::$app->formatter->asDecimal($oldVal) . ' &rarr; ' . Yii::$app->formatter->asDecimal($newVal) . 
        ' (<strong><span class="' . $class . '">' . $sign . $formattedDiff . '</span></strong>)';
}
?>
<div class="trn-greige-stock-history-view">

    <p>
        <?= Html::a('Kembali ke Daftar', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label' => 'Nama Greige',
                        'value' => $model->greige->nama_kain,
                    ],
                    [
                        'label' => 'Alias Greige',
                        'value' => $model->greige->alias,
                    ],
                    [
                        'label' => 'Perubahan Stock',
                        'value' => formatDetailChange($model->stock_old, $model->stock_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Perubahan Available',
                        'value' => formatDetailChange($model->available_old, $model->available_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Perubahan Booked WO',
                        'value' => formatDetailChange($model->booked_wo_old, $model->booked_wo_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Perubahan Booked PFP',
                        'value' => formatDetailChange($model->booked_pfp_old, $model->booked_pfp_new),
                        'format' => 'raw',
                    ],
                    'created_at:datetime',
                    [
                        'label' => 'Diubah Oleh',
                        'value' => $model->createdBy ? $model->createdBy->username : '-',
                    ],
                    'context',
                ],
            ]) ?>
        </div>
    </div>

</div>
