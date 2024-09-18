<?php
use common\models\ar\TrnInspectingItem;
use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnInspecting */
/* @var $modelsItem common\models\ar\TrnInspectingItem[] */
/* @var $indexRoll int*/
/* @var $form ActiveForm */
?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-items',
    'widgetItem' => '.item',
    //'limit' => 4,
    //'min' => 1,
    'insertButton' => '.add-item',
    'deleteButton' => '.remove-item',
    'model' => $modelsItem[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'id',
        'qty',
        'note'
    ],
]); ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>QTY</th>
            <th>NOTE</th>
            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
        </tr>
        </thead>
        <tbody class="container-items">
        <?php foreach ($modelsItem as $index => $modelItem): ?>
            <tr class="item">
                <td>
                    <?php
                    // necessary for update action.
                    if (!$modelItem->isNewRecord) {
                        echo Html::activeHiddenInput($modelItem, "[{$indexRoll}][{$index}]id");
                    }
                    ?>
                    <?= $form->field($modelItem, "[{$indexRoll}][{$index}]qty")->textInput()->label(false) ?>
                </td>
                <td><?= $form->field($modelItem, "[{$indexRoll}][{$index}]note")->textInput()->label(false) ?></td>
                <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

<?php DynamicFormWidget::end(); ?>