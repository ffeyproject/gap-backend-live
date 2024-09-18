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

    <?php $form = ActiveForm::begin(['id'=>'TrnMoFormPrinting']); ?>

    <?=$this->render('_form', ['form'=>$form, 'model'=>$model, 'scGreige'=>$model->scGreige, 'sc'=>$model->sc])?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'design')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-2"><?= $form->field($model, 'border_size')->textInput() ?></div>

                <div class="col-md-2"><?= $form->field($model, 'block_size')->textInput() ?></div>

                <div class="col-md-2">
                    <?= $form->field($model, 'foil')->dropDownList(
                        [ 1 => 'Ya', 0 => 'Tidak'],
                        ['prompt' => 'Pilih ...']
                    ) ?>
                </div>
            </div>

            <?= $form->field($model, 'strike_off')->textarea()?>
        </div>
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

$this->registerJs($jsStr.$this->renderFile(__DIR__.'/js/form-printing.js'), View::POS_END);