<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\rekap\LaporanStockSearch */
/* @var $form ActiveForm */
?>

<div class="trn-stock-greige-search">

    <?php $form = ActiveForm::begin([
        'action' => ['laporan-stock'],
        'method' => 'get',
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Filter</strong></div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    /*echo $form->field($model, 'date')->widget(\kartik\widgets\DatePicker::class, [
                        'readonly' => true,
                        'pluginOptions'=>[
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])->label('Tanggal');*/

                    echo $form->field($model, 'dateRange')->widget(\kartik\daterange\DateRangePicker::class, [
                        //'readonly' => true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'Y-m-d',
                                'separator'=>' to ',
                            ]
                        ]
                    ])->label('Tanggal');
                    ?>
                </div>

                <div class="col-md-2"><?php echo $form->field($model, 'greigeNamaKain') ?></div>

                <div class="col-md-2"><?php echo $form->field($model, 'lot_lusi') ?></div>

                <div class="col-md-2"><?php echo $form->field($model, 'lot_pakan') ?></div>

                <div class="col-md-2">
                    <?= $form->field($model, 'status_tsd')->widget(\kartik\widgets\Select2::class, [
                        'data' => \common\models\ar\TrnStockGreige::tsdOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Kondisi Greige')?>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['laporan-stock'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
