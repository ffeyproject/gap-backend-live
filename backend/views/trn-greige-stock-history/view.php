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

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Detail History</h3>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'created_at',
                        'label' => 'WAKTU',
                        'format' => 'datetime',
                    ],
                    [
                        'label' => 'GREIGE',
                        'value' => $model->greige->nama_kain,
                    ],
                    [
                        'label' => 'STOCK CHANGE',
                        'value' => formatDetailChange($model->stock_old, $model->stock_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'AVAILABLE CHANGE',
                        'value' => formatDetailChange($model->available_old, $model->available_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'BOOKED WO CHANGE',
                        'value' => formatDetailChange($model->booked_wo_old, $model->booked_wo_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'BOOKED PFP CHANGE',
                        'value' => formatDetailChange($model->booked_pfp_old, $model->booked_pfp_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'BOOKED CHANGE',
                        'value' => formatDetailChange($model->booked_old, $model->booked_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'BOOKED OPFP CHANGE',
                        'value' => formatDetailChange($model->booked_opfp_old, $model->booked_opfp_new),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'PERUBAHAN LAINNYA',
                        'value' => call_user_func(function() use ($model) {
                            $fields = [
                                'gap' => 'Gap',
                                'stock_pfp' => 'Stock PFP',
                                'stock_wip' => 'Stock WIP',
                                'stock_ef' => 'Stock EF',
                                'booked_wip' => 'Booked WIP',
                                'booked_ef' => 'Booked EF',
                                'available_pfp' => 'Available PFP',
                                'stock_opname' => 'Stock Opname',
                            ];
                            
                            $changes = [];
                            foreach ($fields as $field => $label) {
                                $oldAttr = $field . '_old';
                                $newAttr = $field . '_new';
                                $oldVal = (float)$model->$oldAttr;
                                $newVal = (float)$model->$newAttr;
                                
                                if ($oldVal !== $newVal) {
                                    $changes[] = Html::tag('div', 
                                        Html::tag('span', $label, ['style' => 'display:inline-block; font-weight:bold;']) . ': ' . 
                                        formatDetailChange($oldVal, $newVal),
                                        ['style' => 'margin-bottom: 2px; font-size: 14px;']
                                    );
                                }
                            }
                            
                            return empty($changes) ? '-' : implode('', $changes);
                        }),
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'context',
                        'label' => 'KONTEKS / TRANSAKSI',
                    ],
                    [
                        'attribute' => 'created_by',
                        'label' => 'OLEH',
                        'value' => $model->createdBy ? $model->createdBy->username : '-',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
