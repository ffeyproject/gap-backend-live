<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\ar\MstJenisHambatan;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProses */
/* @var $form kartik\widgets\ActiveForm */

$existingHambatanIds = [];
if (!$model->isNewRecord) {
    $existingHambatanIds = ArrayHelper::getColumn($model->mstJenisHambatans, 'id');
}
?>

<div class="mst-mesin-proses-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'model_mesin')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Model mesin'
                    ]) ?>

                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'nama_mesin')->textarea([
                            'rows' => 4,
                            'placeholder' => "misalnya input pake pemisah 'enter' atau ','"
                        ])->label('Nama/Nomor mesin') ?>
                    <?php else: ?>
                        <?= $form->field($model, 'nama_mesin')->textInput([
                            'maxlength' => true
                        ])->label('Nama/Nomor mesin') ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="control-label" for="jenis-hambatan-input">Jenis Hambatan</label>
                        <?= Select2::widget([
                            'name' => 'jenis_hambatan',
                            'value' => $existingHambatanIds,
                            'data' => ArrayHelper::map(MstJenisHambatan::find()->asArray()->all(), 'id', 'nama'),
                            'options' => [
                                'id' => 'jenis-hambatan-input',
                                'placeholder' => 'Pilih atau ketik Jenis Hambatan ...',
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', "\n", "\r"],
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
