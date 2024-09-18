<?php
use common\models\ar\TrnTerimaMakloonFinishItem;
use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinish */
/* @var $modelsItem TrnTerimaMakloonFinishItem[] */
/* @var $form ActiveForm */
?>

<?php
DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    //'limit' => 4, // the maximum times, an element can be cloned (default 999)
    //'min' => 0, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $modelsItem[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'qty',
        'note',
    ],

]);
?>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>Qty</th>
        <th>Note</th>
        <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
    </tr>
    </thead>
    <tbody class="container-items">
    <?php foreach ($modelsItem as $index => $modelItem): ?>
        <?php
        // necessary for update action.
        if (! $modelItem->isNewRecord) {
            echo Html::activeHiddenInput($modelItem, "[{$index}]id");
        }
        ?>
        <tr class="item">
            <td>
                <?php
                echo $form->field($modelItem, "[{$index}]qty")->textInput()->label(false);
                ?>
            </td>
            <td><?= $form->field($modelItem, "[{$index}]note")->textInput()->label(false) ?></td>
            <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php DynamicFormWidget::end();?>