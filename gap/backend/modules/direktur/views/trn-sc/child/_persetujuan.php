<?php

use common\models\ar\TrnSc;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnSc */
?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header"><h3 class="box-title">Persetujuan Direktur</h3></div>
            <div class="box-body">
                <p><?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'direkturName',
                            'apvDirStatus',
                            'apv_dir_at:datetime'
                        ],
                    ]) ?></p>

                <?php
                $dataProvider = new ArrayDataProvider([
                    'allModels' => \yii\helpers\Json::decode($model->reject_note_dir),
                    'pagination' => false,
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => '<strong>Catatan Penolakan</strong>',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date_time',
                        'note'
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header"><h3 class="box-title">Persetujuan Manager</h3></div>
            <div class="box-body">
                <p><?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'managerName',
                            'apvMgrStatus',
                            'apv_mgr_at:datetime',
                        ],
                    ]) ?></p>

                <?php
                $dataProvider = new ArrayDataProvider([
                    'allModels' => \yii\helpers\Json::decode($model->reject_note_mgr),
                    'pagination' => false,
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => '<strong>Catatan Penolakan</strong>',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date_time',
                        'note'
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>