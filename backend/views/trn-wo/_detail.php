<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model TrnWo */
/* @var $mo TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */
/* @var $stockM string */
/* @var $bookedM string */
/* @var $stockLabel string */
/* @var $bookkLabel string */
/* @var $avM string */
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            WO Detail - Greige: <?=$model->greige->nama_kain?> (Alias: <?=$model->greige->alias?>) - <?=$stockLabel?>: <?=Yii::$app->formatter->asDecimal($avM)?>M


            <?php
            Modal::begin([
                'header' => '<h2>Info Stock</h2>',
                'toggleButton' => ['label' => 'Info Stock', 'class'=>'btn btn-info btn-xs'],
                'size' => 'modal-lg',
            ]);
            //echo 'Say hello...';
            ?>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>GREIGE</th>
                    <th>STOCK</th>
                    <th>BOOKED BY WO</th>
                    <th>AVAILABLE</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$model->greige->nama_kain?></td>
                    <td><?=Yii::$app->formatter->asDecimal($model->greige->stock)?></td>
                    <td>
                        <?php
                        $woItems = TrnWo::find()->select('no')->where(['greige_id'=>$model->greige_id, 'status'=>TrnWo::STATUS_APPROVED])->asArray()->all();
                        foreach ($woItems as $i=>$woItem) {
                            if($i > 0){
                                echo '<br>';
                            }

                            echo $woItem['no'];
                        }
                        ?>
                    </td>
                    <td><?=Yii::$app->formatter->asDecimal($model->greige->available)?></td>
                </tr>
                </tbody>
            </table>

            <?php Modal::end();?>
        </h3>
        <div class="box-tools pull-right">
            <?=Html::a('<span class="glyphicon glyphicon-print" aria-hidden="true"></span>', ['print', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-default',
                'title' => 'Print',
                'target' => 'blank'
            ])?>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'id',
                        'date:date',
                        'no_urut',
                        'no',
                        [
                            'label' => 'Jenis Order',
                            'value' => TrnSc::jenisOrderOptions()[$model->jenis_order]
                        ],
                        [
                            'attribute' => 'papper_tube_id',
                            'value' => $model->papperTube->name
                        ],
			[
                            'attribute' => 'Persen Grading Pengkartuan Greige A/B',
                            'value' => $mo->persen_grading
                        ],
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'plastic_size',
                        'created_at:datetime',
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        [
                            'label'=>'Handling',
                            'value'=>$model->handling->name
                        ],
                        'tgl_kirim:date',
                        //'creatorName',
                        //'batal:boolean'
                    ],
                ]) ?>
            </div>
        </div>

        <p>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'shipping_mark:html',
                    'note:html',
                    'note_two:html',
                ],
            ]) ?>
        </p>
    </div>
</div>
