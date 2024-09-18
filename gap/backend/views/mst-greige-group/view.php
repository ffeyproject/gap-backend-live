<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreigeGroup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greige Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="mst-greige-group-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'jenis_kain',
                        'value' => $model::jenisKainOptions()[$model->jenis_kain],
                    ],
                    [
                        'attribute' => 'lebar_kain',
                        'value' => $model::lebarKainOptions()[$model->lebar_kain],
                    ],
                    'nama_kain',
                    'qty_per_batch:decimal',
                    [
                        'attribute' => 'unit',
                        'value' => $model::unitOptions()[$model->unit],
                    ],
                    'nilai_penyusutan',
                    'gramasi_kain',
                    'sulam_pinggir',
                    'created_at:datetime',
                    'created_by',
                    'updated_at:datetime',
                    'updated_by',
                    'aktif:boolean',
                ],
            ]) ?>
        </div>
    </div>

</div>
