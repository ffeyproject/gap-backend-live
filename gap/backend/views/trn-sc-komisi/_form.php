<?php

use common\models\ar\TrnScGreige;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScKomisi */
/* @var $form yii\widgets\ActiveForm */

$sc = $model->sc;
?>

<div class="trn-sc-komisi-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnScKomisiForm']); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            $agens = ArrayHelper::map($sc->trnScAgens, 'id', 'nama_agen');
            echo $form->field($model, 'sc_agen_id')->widget(Select2::class, [
                'data' => $agens,
                'options' => ['placeholder' => 'Pilih Agen ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            $greiges = ArrayHelper::map($sc->trnScGreiges, 'id', function($data){
                /* @var $data TrnScGreige*/
                $procc = TrnScGreige::processOptions()[$data->process];
                $qty = Yii::$app->formatter->asDecimal($data->qty);
                return $data->greigeGroup->nama_kain.' ('.$procc.') '.$qty.' Batch';
            });
            echo $form->field($model, 'sc_greige_id')->widget(Select2::class, [
                'data' => $greiges,
                'options' => ['placeholder' => 'Pilih Greige ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'tipe_komisi')->widget(Select2::class, [
                'data' => $model::tipeKomisiOptions(),
                'options' => ['placeholder' => 'Pilih Tipe Komisi ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'komisi_amount')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'scKomisiFormJs');
