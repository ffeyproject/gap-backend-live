<?php

use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreige;
use common\widgets\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use backend\models\ar\StockGreige;
use backend\models\form\StockGreigeForm;

/* @var $this yii\web\View */
/* @var $model StockGreigeForm */
/* @var $form kartik\widgets\ActiveForm */
/* @var $modelsStock StockGreige[] */
?>

<div class="trn-stock-greige-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?php
                            $ajaxUrl = Url::to(['ajax/lookup-greige']);
                            $greige = empty($model->greige_id) ? '' : MstGreige::findOne($model->greige_id)->nama_kain;
                            echo $form->field($model, 'greige_id')->widget(Select2::class, [
                                'initValueText' => $greige, // set the initial display text
                                'options' => ['placeholder' => 'Cari greige...'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => $ajaxUrl,
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                    ],
                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                    'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                                    'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                                ]
                            ]);?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'no_lapak')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'lot_lusi')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'lot_pakan')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'status_tsd')->widget(Select2::classname(), [
                                'data' => StockGreige::tsdOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'mengetahui')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $asalGreiges = [
                                TrnStockGreige::ASAL_GREIGE_WJL => TrnStockGreige::asalGreigeOptions()[TrnStockGreige::ASAL_GREIGE_WJL],
                                TrnStockGreige::ASAL_GREIGE_RAPIER => TrnStockGreige::asalGreigeOptions()[TrnStockGreige::ASAL_GREIGE_RAPIER]
                            ]; //hanya bisa dari dua asal greige ini

                            echo $form->field($model, 'asal_greige')->widget(Select2::classname(), [
                                'data' => $asalGreiges,
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>

                        <div class="col-md-6"><?= $form->field($model, 'no_document')->textInput(['maxlength' => true]) ?></div>
                    </div>

                    <?= $form->field($model, 'note')->textInput() ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3>
                    <div class="box-tools pull-right"></div>
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
                        'model' => $modelsStock[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'no_set_lusi',
                            'panjang_m',
                        ],

                    ]);
                    ?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>NO</th>
                            <th>Grade</th>
                            <th>No. MC Weaving</th>
                            <th>Qty</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php
                        $grdOptsA = [''=>'--'];
                        $grdOptsB = StockGreige::gradeOptions();
                        $grdOpts = ArrayHelper::merge($grdOptsA, $grdOptsB);
                        ?>
                        <?php foreach ($modelsStock as $index => $modelStock): ?>
                            <tr class="item">
                                <td class="panel-title-address"><?=$index + 1?></td>
                                <td>
                                    <?php
                                    echo $form->field($modelStock, "[{$index}]grade")->dropDownList($grdOpts)->label(false);
                                    ?>
                                </td>
                                <td><?= $form->field($modelStock, "[{$index}]no_set_lusi")->textInput()->label(false) ?></td>
                                <td><?= $form->field($modelStock, "[{$index}]panjang_m")->textInput(['class'=>'form-control panjang_unit'])->label(false) ?></td>
                                <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>NO</th>
                            <th>Grade</th>
                            <th>No. MC Weaving</th>
                            <th>Qty</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </tfoot>
                    </table>
                    <?php DynamicFormWidget::end();?>

                    <p><strong>TOTAL: <span id="TotalLength">0</span></strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs($this->render('js/form-dua.js'), $this::POS_END);
