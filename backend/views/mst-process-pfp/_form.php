<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\ar\MstMesinProses;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPfp */
/* @var $form yii\widgets\ActiveForm */

// Get distinct machine models
$modelsList = MstMesinProses::find()
    ->select(['model_mesin'])
    ->distinct()
    ->asArray()
    ->all();
$modelsMap = [];
foreach ($modelsList as $m) {
    $val = $m['model_mesin'] ? $m['model_mesin'] : '_empty_';
    $label = $m['model_mesin'] ? $m['model_mesin'] : 'Tanpa Model';
    $modelsMap[$val] = $label;
}

// Find pre-selected models if updating
$selectedModelMesin = [];
if (!$model->isNewRecord && !empty($model->mesin_proses_ids)) {
    $machines = MstMesinProses::find()->where(['id' => $model->mesin_proses_ids])->all();
    foreach ($machines as $firstMachine) {
        $val = $firstMachine->model_mesin ? $firstMachine->model_mesin : '_empty_';
        if (!in_array($val, $selectedModelMesin)) {
            $selectedModelMesin[] = $val;
        }
    }
}

// Initial machines map for the selected models
$machinesMap = [];
if (!empty($selectedModelMesin)) {
    $machinesQuery = MstMesinProses::find();
    $orConditions = ['or'];
    foreach ($selectedModelMesin as $sm) {
        if ($sm === '_empty_') {
            $orConditions[] = ['or', ['model_mesin' => null], ['model_mesin' => '']];
        } else {
            $orConditions[] = ['model_mesin' => $sm];
        }
    }
    $machinesQuery->andWhere($orConditions);
    $machinesMap = ArrayHelper::map($machinesQuery->asArray()->all(), 'id', 'nama_mesin');
}
?>

<div class="mst-process-pfp-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'nama_proses')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'order')->textInput() ?>

                    <?= $form->field($model, 'max_pengulangan')->textInput() ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'tanggal')->checkbox() ?>

                    <?= $form->field($model, 'start')->checkbox() ?>

                    <?= $form->field($model, 'stop')->checkbox() ?>

                    <div class="form-group">
                        <label class="control-label">Model Mesin</label>
                        <?= Select2::widget([
                            'name' => 'model_mesin',
                            'value' => $selectedModelMesin,
                            'data' => $modelsMap,
                            'options' => [
                                'id' => 'model-mesin-select',
                                'placeholder' => 'Pilih Model Mesin ...',
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>
                    </div>

                    <?= $form->field($model, 'mesin_proses_ids')->widget(Select2::classname(), [
                        'data' => $machinesMap,
                        'options' => [
                            'id' => 'machine-select',
                            'placeholder' => 'Pilih Mesin ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Mesin') ?>

                    <?= $form->field($model, 'no_mesin')->checkbox() ?>

                    <?= $form->field($model, 'shift_operator')->checkbox() ?>

                    <?= $form->field($model, 'temp')->checkbox() ?>

                    <?= $form->field($model, 'speed')->checkbox() ?>

                    <?= $form->field($model, 'waktu')->checkbox() ?>

                    <?= $form->field($model, 'program_number')->checkbox() ?>

                    <?= $form->field($model, 'ex_relax')->checkbox() ?>

                    <?= $form->field($model, 'ex_wr_oligomer')->checkbox() ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'ex_dyeing')->checkbox() ?>

                    <?= $form->field($model, 'wr_pcnt')->checkbox() ?>

                    <?= $form->field($model, 'rpm')->checkbox() ?>

                    <?= $form->field($model, 'density')->checkbox() ?>

                    <?= $form->field($model, 'jamur')->checkbox() ?>

                    <?= $form->field($model, 'karat')->checkbox() ?>

                    <?= $form->field($model, 'over_feed')->checkbox() ?>

                    <?= $form->field($model, 'counter')->checkbox() ?>
                    
                    <?= $form->field($model, 'panjang_jadi')->checkbox() ?>

                    <?= $form->field($model, 'lebar_jadi')->checkbox() ?>

                    <?= $form->field($model, 'info_kualitas')->checkbox() ?>

                    <?= $form->field($model, 'gangguan_produksi')->checkbox() ?>
                    
                    <?= $form->field($model, 'keterangan')->checkbox() ?>

                    <?= $form->field($model, 'gramasi')->checkbox() ?>
                    
                    <?= $form->field($model, 'use_jetblack')->checkbox() ?>
                    
                    <?= $form->field($model, 'perbaikan')->checkbox() ?>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$machinesUrl = \yii\helpers\Url::to(['/trn-hambatan-mesin/get-machines-by-model']);

$js = <<<JS
$('#model-mesin-select').on('change', function() {
    var modelVal = $(this).val();
    $('#machine-select').empty().trigger('change');
    if (!modelVal || modelVal.length === 0) return;
    
    $.ajax({
        url: '{$machinesUrl}',
        data: { model_mesin: modelVal },
        dataType: 'json',
        success: function(data) {
            $.each(data, function(i, item) {
                var option = new Option(item.nama_mesin, item.id, false, false);
                $('#machine-select').append(option);
            });
            $('#machine-select').trigger('change');
        }
    });
});
JS;
$this->registerJs($js);
?>
