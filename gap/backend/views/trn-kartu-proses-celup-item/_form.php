<?php
use common\models\ar\TrnStockGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelupItem */
/* @var $stockOptionListMap array */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchHint string*/
?>

    <div class="kartu-proses-celup-item-form">

        <?php $form = ActiveForm::begin(['id'=>'KartuProsesItemForm']); ?>

        <?= $form->field($model, 'stock_id')->widget(Select2::classname(), [
            'data' => $stockOptionListMap,
            'options' => ['placeholder' => 'Pilih stock ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->hint($searchHint)?>

        <div class="row">
            <div class="col-md-4">
                <?=$form->field($model, 'tube')->widget(Select2::classname(), [
                    'data' => $model::tubeOptions(),
                    'options' => ['placeholder' => 'Pilih ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])?>
            </div>
        </div>

        <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'kartuProsesItemFormJs');