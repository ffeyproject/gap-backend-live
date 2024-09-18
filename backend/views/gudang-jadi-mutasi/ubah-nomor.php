<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */
/* @var $form ActiveForm */

$this->title = 'Ubah Nomor Gudang Jadi Mutasi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah Nomor';
?>

<div class="gudang-jadi-mutasi-update">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'no_urut')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nomor')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
