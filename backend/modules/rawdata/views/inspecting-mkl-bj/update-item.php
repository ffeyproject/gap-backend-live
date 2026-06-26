<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ar\InspectingMklBjItems;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBjItems */

$this->title = 'Update Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Mkl Bjs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->inspecting_id, 'url' => ['view', 'id' => $model->inspecting_id]];
$this->params['breadcrumbs'][] = 'Update Item';
?>
<div class="inspecting-mkl-bj-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="inspecting-mkl-bj-item-form box box-primary">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'grade')->dropDownList(InspectingMklBjItems::gradeOptions()) ?>
            
            <?= $form->field($model, 'defect')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'lot_no')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'qty')->textInput() ?>

            <?= $form->field($model, 'join_piece')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'no_urut')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['view', 'id' => $model->inspecting_id], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
