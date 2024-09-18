<?php
use common\models\ar\TrnMo;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnMo */
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><strong>Persetujuan</strong></h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'approvalName',
                    ],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'approvalStatus',
                    ],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'approved_at:datetime',
                    ],
                ]) ?>
            </div>
        </div>

        <?php
        $dataProvider = new ArrayDataProvider([
            'allModels' => Json::decode($model->reject_notes),
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
