<?php

use common\models\ar\MstK3l;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstK3l */

$this->title = $model->k3l_code;
$this->params['breadcrumbs'][] = ['label' => 'Master K3L', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="mst-k3l-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->k3l_code], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->k3l_code], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus data ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Tambah Baru', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Detail Master K3L</h3>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'k3l_code',
                    'k3l_desc',
                    [
                        'attribute' => 'k3l_active',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /* @var $model MstK3l */
                            if ($model->k3l_active === MstK3l::STATUS_AKTIF) {
                                return '<span class="label label-success">Aktif</span>';
                            }
                            return '<span class="label label-danger">Tidak Aktif</span>';
                        },
                    ],
                    [
                        'attribute' => 'k3l_add_by',
                        'label' => 'Dibuat Oleh',
                        'value' => $model->createdBy ? $model->createdBy->username : $model->k3l_add_by,
                    ],
                    'k3l_add_date:datetime',
                    [
                        'attribute' => 'k3l_upd_by',
                        'label' => 'Diubah Oleh',
                        'value' => $model->updatedBy ? $model->updatedBy->username : $model->k3l_upd_by,
                    ],
                    'k3l_upd_date:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>
