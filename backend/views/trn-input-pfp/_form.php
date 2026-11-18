<?php
use common\models\ar\MstGreige;
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnBuyPfpItem;
use common\models\ar\TrnStockGreige;
use common\widgets\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model TrnBuyPfp */
/* @var $modelsItem TrnBuyPfpItem[] */
/* @var $form ActiveForm */
?>

<div class="trn-input-pfp-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?php
                            $ajaxUrl = Url::to(['ajax/lookup-greige']);
                            $greige = empty($model->greige_id) ? '' : MstGreige::findOne($model->greige_id)->nama_kain;
                            echo $form->field($model, 'greige_id')->widget(Select2::class, [
                                'initValueText' => $greige,
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
                            ]); ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'no_document')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'jenis')->widget(Select2::class, [
                                'data' => $model::jenisOptions(),
                                'options' => ['placeholder' => 'Pilih Jenis...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'jenis_printing')->widget(Select2::class, [
                                'data' => TrnStockGreige::jenisPrintingOptions(),
                                'options' => ['placeholder' => 'Pilih Jenis Printing...'],
                                'pluginOptions' => [
                                    'allowClear' => false
                                ],
                            ])->label('Jenis Printing <span class="text-danger">*</span>') ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'no_referensi')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'vendor')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Calculator</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="inputYard" placeholder="Panjang Dalam Yard">
                                    <div class="input-group-addon">Yard</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <?= Html::button('Konversi <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>', ['id' => 'BtnKonversi', 'class' => 'btn btn-success btn-flat btn-block']) ?>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="inMeterRes" placeholder="Panjang Dalam Meter" disabled>
                                    <div class="input-group-addon">Meter</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        'widgetContainer' => 'dynamicform_wrapper',
                        'widgetBody' => '.container-items',
                        'widgetItem' => '.item',
                        'insertButton' => '.add-item',
                        'deleteButton' => '.remove-item',
                        'model' => $modelsItem[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'panjang_m',
                        ],
                    ]);
                    ?>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty (M)</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($modelsItem as $index => $modelItem): ?>
                            <tr class="item">
                                <?php
                                if (!$modelItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelItem, "[{$index}]id");
                                }
                                ?>
                                <td><span class="panel-title-address"><?= ($index + 1) ?></span></td>
                                <td><?= $form->field($modelItem, "[{$index}]panjang_m")->textInput()->label(false) ?></td>
                                <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty (M)</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                    </table>

                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$href = Url::to(['/ajax/converter-yard-to-meter']);
$yToM = Yii::$app->params['yardToMeter'];
$js = <<<JS
var hrefConvert = "{$href}"; 
var yToM = "{$yToM}";
JS;

$this->registerJs($js . $this->renderFile(__DIR__ . '/js/form.js'), View::POS_END);
