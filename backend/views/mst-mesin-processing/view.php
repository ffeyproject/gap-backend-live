<?php

use kartik\dialog\Dialog;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use common\models\ar\MstGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */

$namaMesinStr = is_array($model->nama_mesin) ? implode(', ', $model->nama_mesin) : $model->nama_mesin;
$this->title = $namaMesinStr;
$this->params['breadcrumbs'][] = ['label' => 'Mesin Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);


?>

<div class="mst-mesin-processing-view">
    <div class="row" style="margin-bottom: 20px; display: flex; align-items: center; flex-wrap: wrap;">
        <div class="col-md-8">
            <h1 style="margin-top: 0; font-weight: 700; color: #2c3e50; font-size: 28px;">
                <i class="fa fa-industry text-primary" style="margin-right: 10px;"></i> <?= Html::encode($namaMesinStr) ?>
            </h1>
            <p class="text-muted" style="font-size: 14px; margin-left: 40px;">
                <i class="fa fa-info-circle"></i> Master Data Detail untuk Mesin Processing - ID: #<?= $model->id ?>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->id], [
                'class' => 'btn btn-primary', 
                'style'=>'border-radius: 8px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);'
            ]) ?>
            <?= Html::a('<i class="fa fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'style'=>'border-radius: 8px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2); margin-left: 5px;',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <!-- Relax Section -->
        <div class="col-md-6">
            <div class="box box-solid box-info" style="border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08); border: none;">
                <div class="box-header" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 18px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 18px; letter-spacing: 0.5px;">
                        <i class="fa fa-refresh" style="margin-right: 8px;"></i> KATEGORI: RELAX
                    </h3>
                </div>
                <div class="box-body" style="padding: 25px; background: #fff;">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover detail-view', 'style'=>'margin-bottom: 0; border: none;'],
                        'attributes' => [
                            [
                                'attribute' => 'relax_mesin',
                                'label' => 'Mesin',
                                'captionOptions' => ['style' => 'width: 35%; font-weight: 600; color: #7f8c8d; border-top: none;'],
                                'contentOptions' => ['style' => 'border-top: none; font-weight: 500;']
                            ],
                            [
                                'attribute' => 'relax_jenis_nozzle',
                                'label' => 'Jenis Nozzle',
                                'contentOptions' => ['style' => 'font-weight: 500;']
                            ],
                            [
                                'attribute' => 'relax_ukuran_nozzle',
                                'label' => 'Ukuran Nozzle',
                                'contentOptions' => ['style' => 'font-weight: 500;']
                            ],
                            [
                                'attribute' => 'relax_catatan',
                                'label' => 'Catatan',
                                'format' => 'ntext',
                                'contentOptions' => ['style' => 'font-style: italic; color: #5a6772; background: #fcfdfd; border-radius: 4px; padding: 10px;']
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Celup Section -->
        <div class="col-md-6">
            <div class="box box-solid box-success" style="border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08); border: none;">
                <div class="box-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 18px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 18px; letter-spacing: 0.5px;">
                        <i class="fa fa-tint" style="margin-right: 8px;"></i> KATEGORI: CELUP
                    </h3>
                </div>
                <div class="box-body" style="padding: 25px; background: #fff;">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover detail-view', 'style'=>'margin-bottom: 0; border: none;'],
                        'attributes' => [
                            [
                                'attribute' => 'celup_mesin',
                                'label' => 'Mesin',
                                'captionOptions' => ['style' => 'width: 35%; font-weight: 600; color: #7f8c8d; border-top: none;'],
                                'contentOptions' => ['style' => 'border-top: none; font-weight: 500;']
                            ],
                            [
                                'attribute' => 'celup_jenis_nozzle',
                                'label' => 'Jenis Nozzle',
                                'contentOptions' => ['style' => 'font-weight: 500;']
                            ],
                            [
                                'attribute' => 'celup_ukuran_nozzle',
                                'label' => 'Ukuran Nozzle',
                                'contentOptions' => ['style' => 'font-weight: 500;']
                            ],
                            [
                                'attribute' => 'celup_catatan',
                                'label' => 'Catatan',
                                'format' => 'ntext',
                                'contentOptions' => ['style' => 'font-style: italic; color: #5a6772; background: #fcfdfd; border-radius: 4px; padding: 10px;']
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>


</div>
