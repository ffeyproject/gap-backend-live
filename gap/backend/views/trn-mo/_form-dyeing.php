<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */
/* @var $form ActiveForm */
?>

<div class="trn-mo-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnMoFormDyeing']); ?>

    <?=$this->render('_form', ['form'=>$form, 'model'=>$model, 'scGreige'=>$model->scGreige, 'sc'=>$model->sc])?>

    <div class="box">
        <div class="box-body"><?= $form->field($model, 'sulam_pinggir')->textInput(['maxlength' => true]) ?></div>
        <div class="box-body"><?= $form->field($model, 'no_lab_dip')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$reWoUrl = Url::to(['/ajax/lookup-re-wo']);
$jsStr = <<<JS
var reWoUrl = "{$reWoUrl}";
JS;

$this->registerJs($jsStr.$this->renderFile(__DIR__.'/js/form-dyeing.js'), View::POS_END);