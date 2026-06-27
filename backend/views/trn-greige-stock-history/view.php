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

$fields = [
    'gap' => 'Gap',
    'stock' => 'Stock',
    'available' => 'Available',
    'booked_wo' => 'Booked WO',
    'stock_pfp' => 'Stock PFP',
    'stock_wip' => 'Stock WIP',
    'stock_ef' => 'Stock EF',
    'booked' => 'Booked',
    'booked_pfp' => 'Booked PFP',
    'booked_wip' => 'Booked WIP',
    'booked_ef' => 'Booked EF',
    'booked_opfp' => 'Booked OPFP',
    'available_pfp' => 'Available PFP',
    'stock_opname' => 'Stock Opname',
];
?>
<div class="trn-greige-stock-history-view">

    <p>
        <?= Html::a('Kembali ke Daftar', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Transaksi</h3>
                </div>
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
                                'value' => $model->greige->alias ? $model->greige->alias : '-',
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

        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Perubahan Stok Terdeteksi</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Field</th>
                                <th>Perubahan Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $hasChanges = false;
                            foreach ($fields as $field => $label): 
                                $oldAttr = $field . '_old';
                                $newAttr = $field . '_new';
                                if ((float)$model->$oldAttr !== (float)$model->$newAttr):
                                    $hasChanges = true;
                            ?>
                                <tr>
                                    <td><strong><?= Html::encode($label) ?></strong></td>
                                    <td><?= formatDetailChange($model->$oldAttr, $model->$newAttr) ?></td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 
                            if (!$hasChanges):
                            ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada perubahan nilai terdeteksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Seluruh Parameter Stok (Nilai Transisi)</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <?php 
                $count = 0;
                foreach ($fields as $field => $label): 
                    $oldAttr = $field . '_old';
                    $newAttr = $field . '_new';
                    $isChanged = (float)$model->$oldAttr !== (float)$model->$newAttr;
                ?>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <div class="panel panel-default <?= $isChanged ? 'panel-info' : '' ?>" style="margin-bottom: 0;">
                            <div class="panel-heading" style="padding: 5px 10px;">
                                <strong><?= Html::encode($label) ?></strong>
                            </div>
                            <div class="panel-body" style="padding: 10px;">
                                <?= formatDetailChange($model->$oldAttr, $model->$newAttr) ?>
                            </div>
                        </div>
                    </div>
                <?php 
                endforeach; 
                ?>
            </div>
        </div>
    </div>

</div>
