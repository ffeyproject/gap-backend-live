<?php
use backend\models\form\CatatanProsesForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CatatanProsesForm */
?>

<div class="kartu-proses-dyeing-form">
    <?php $form = ActiveForm::begin(['id'=>'AddHasilTesGosokProsesForm']); ?>

    <?= $form->field($model, 'hasil_tes_gosok')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/add-hasil-tes-gosok.js'), $this::POS_END, 'kartuProsesItemFormJs');