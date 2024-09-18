<?php
use common\widgets\dynamicform\DynamicFormWidget;
use common\models\ar\MstGreigeGroup;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2 */
/* @var $modelsItem common\models\ar\TrnKirimMakloonV2Item[] */
/* @var $form ActiveForm */

$this->registerJs('
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .nomorUrut").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .nomorUrut").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
')
?>

<div class="trn-kirim-makloon-v2-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);?>

    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php
                            if($model->isNewRecord){
                                $wo = $model->wo_id === null ? '' : $model->wo->no;
                                echo $form->field($model, 'wo_id')->widget(Select2::class, [
                                    'initValueText' => $wo, // set the initial display text
                                    'options' => ['placeholder' => 'Cari WO...'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => Url::to(['ajax/lookup-wo-all']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                        'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                                    ],
                                    /*'pluginEvents' => [
                                        'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                        'select2:unselect' => 'function(e){$("#trnkartuprosesdyeing-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesdyeing-wo_color_id").empty();}'
                                    ]*/
                                ])->label('Nomor Working Order');
                            }else{
                                echo '<div class="form-group"><label>Nomor WO</label><input type="text" class="form-control" value="'.$model->wo->no.'" disabled></div>';
                            }
                            ?>
                        </div>

                        <div class="col-md-4">
                            <?=$form->field($model, 'date')->widget(\kartik\widgets\DatePicker::classname(), [
                                'options' => ['placeholder' => 'Pilih Tanggal ...'],
                                'readonly' => true,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    //'todayBtn' => true,
                                ]
                            ])?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'vendor_id')->widget(Select2::classname(), [
                                'data' => \common\models\ar\MstVendor::optionList(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-6"><?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?></div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3>
                </div>
                <div class="box-body">
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
                            'id',
                            'kirim_makloon_id',
                            'qty',
                            'note'
                        ],

                    ]);
                    ?>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($modelsItem as $index => $modelItem): ?>
                            <tr class="item">
                                <?php
                                // necessary for update action.
                                if (!$modelItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelItem, "[{$index}]id");
                                }
                                ?>
                                <td><span class="nomorUrut"><?= ($index + 1) ?></span></td>
                                <td><?= $form->field($modelItem, "[{$index}]qty")->textInput()->label(false) ?></td>
                                <td>
                                    <?php
                                    echo $form->field($modelItem, "[{$index}]note")->textInput()->label(false);
                                    ?>
                                </td>
                                <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                    </table>

                    <?php DynamicFormWidget::end();?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
