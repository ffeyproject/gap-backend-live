<?php
use common\models\ar\TrnInspectingRoll;
use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnInspecting */
/* @var $modelsRoll common\models\ar\TrnInspectingRoll[] */
/* @var $modelsItem common\models\ar\TrnInspectingItem[] */
/* @var $form ActiveForm */
?>

<?php
/*
$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-roll").each(function(index) {
        jQuery(this).html("Roll: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-roll").each(function(index) {
        jQuery(this).html("Roll: " + (index + 1))
    });
});
JS;
$this->registerJs($js);
*/

DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-rolls', // required: css class selector
    'widgetItem' => '.roll', // required: css class
    //'limit' => 4, // the maximum times, an element can be cloned (default 999)
    //'min' => 0, // 0 or 1 (default 1)
    'insertButton' => '.add-roll', // css class
    'deleteButton' => '.remove-roll', // css class
    'model' => $modelsRoll[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'id',
        'grade',
    ],
]);
?>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Rolls</th>
            <th >Items</th>
            <th class="text-center" style="width: 90px;">
                <button type="button" class="add-roll btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-rolls">
        <?php foreach ($modelsRoll as $indexRoll => $modelRoll): ?>
            <tr class="roll">
                <td class="vcenter">
                    <?php
                    if (! $modelRoll->isNewRecord) {
                        echo Html::activeHiddenInput($modelRoll, "[{$indexRoll}]id");
                    }

                    /*echo $form->field($modelItem, "[{$index}]grade")->widget(Select2::classname(), [
                        'data' => \common\models\ar\TrnInspectingItem::gradeOptions(),
                        'options' => ['placeholder' => 'Select ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        ])->label(false);*/

                    $opts = \yii\helpers\ArrayHelper::merge([''=>'Pilih...'], TrnInspectingRoll::gradeOptions());
                    echo $form->field($modelRoll, "[{$indexRoll}]grade")->dropDownList($opts)->label(false);
                    ?>
                </td>
                <td>
                    <?=$this->render('_form_items', ['form'=>$form, 'model'=>$model, 'indexRoll'=>$indexRoll, 'modelsItem'=>$modelsItem[$indexRoll]])?>
                </td>
                <td class="text-center vcenter" style="width: 90px; verti">
                    <button type="button" class="remove-roll btn btn-danger btn-xs"><span class="fa fa-minus"></span></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th>Rolls</th>
            <th >Items</th>
            <th class="text-center" style="width: 90px;">
                <button type="button" class="add-roll btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            </th>
        </tr>
        </tfoot>
    </table>

<?php
DynamicFormWidget::end();
