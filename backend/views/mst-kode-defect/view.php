<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstKodeDefect */

$this->title = $model->kode;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Kode Defect', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="mst-kode-defect-view">
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

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no_urut',
                            'kode',
                            'nama_defect',
                            'asal_defect',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Related Defect Inspecting Items <?= date('F Y') ?></h3>
                    <div class="box-tools pull-right">
                        <?php $count = 0; ?>
                        <?php foreach ($model->defectInspectingItems as $defectInspectingItem): ?>
                        <?php 
                    // Pastikan inspectingItem ada
                    $inspectingItem = $defectInspectingItem->inspectingItem;
                    if ($inspectingItem) {
                        // Pastikan inspectingModel ada
                        $inspectingModel = $inspectingItem->inspecting;
                        if ($inspectingModel) {
                            // Periksa jika tanggal created_at cocok dengan bulan dan tahun saat ini
                            if (date('Y-m', strtotime($inspectingModel->date)) === date('Y-m')) {
                                $count++; // Hitung item yang cocok
                            }
                        }
                    }
                ?>
                        <?php endforeach; ?>
                        <span class="label label-primary"><?= $count ?></span>
                    </div>
                </div>
                <div class="box-body">
                    <div class="list-group">
                        <?php foreach ($model->defectInspectingItems as $defectInspectingItem): ?>
                        <?php 
                    // Ambil inspectingItem
                    $inspectingItem = $defectInspectingItem->inspectingItem;
                    if ($inspectingItem) {
                        // Ambil inspectingModel
                        $inspectingModel = $inspectingItem->inspecting;
                        if ($inspectingModel) {
                            // Cek jika tanggal created_at cocok dengan bulan dan tahun saat ini
                            if (date('Y-m', strtotime($inspectingModel->date)) === date('Y-m')) {
                                echo Html::a(
                                    $inspectingModel->id, // Menampilkan ID atau atribut lain, misalnya $inspectingModel->no
                                    ['/trn-inspecting/view', 'id' => $inspectingModel->id],
                                    ['class' => 'list-group-item']
                                );
                            }
                        }
                    }
                ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>