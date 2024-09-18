<?php
use common\models\ar\TrnWo;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnWo */
?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Persetujuan Kabag PMC</h3>
            </div>

            <div class="box-body">
                <p>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'mengetahuiName',
                            'apv_mengetahui_at:datetime',
                        ],
                    ]) ?>
                </p>

                <?php
                $dataProvider = new ArrayDataProvider([
                    'allModels' => Json::decode($model->reject_note_mengetahui),
                    'pagination' => false,
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => '<strong>Riwayat Penolakan</strong>',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date_time:datetime',
                        'note'
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Persetujuan Marketing</h3>
            </div>

            <div class="box-body">
                <p>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'marketingName',
                            'apv_marketing_at:datetime',
                        ],
                    ]) ?>
                </p>

                <?php
                $dataProvider = new ArrayDataProvider([
                    'allModels' => Json::decode($model->reject_note_marketing),
                    'pagination' => false,
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => '<strong>Riwayat Penolakan</strong>',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date_time:datetime',
                        'note'
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>