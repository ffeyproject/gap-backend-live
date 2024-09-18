<?php
use common\models\ar\MstHandling;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderCelup */
/* @var $form ActiveForm */
?>

<div class="trn-order-pfp-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $ajaxUrl = Url::to(['ajax/lookup-sc']);
                    $greigeLbl = empty($model->sc_id) ? '' : $model->sc->no;
                    echo $form->field($model, 'sc_id')->widget(Select2::class, [
                        'initValueText' => $greigeLbl, // set the initial display text
                        'options' => ['placeholder' => 'Cari ...'],
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
                            'templateResult' => new JsExpression('function(sc) { return sc.text; }'),
                            'templateSelection' => new JsExpression('function (sc) { return sc.text; }'),
                        ]
                    ])->label('Pilih Kontrak Pemesaan / SC');
                    ?>

                    <?php
                    $ajaxUrl = Url::to(['ajax/lookup-greige']);
                    $greigeLbl = empty($model->greige_id) ? '' : $model->greige->nama_kain.' (Alias: '.$model->greige->alias.')';
                    echo $form->field($model, 'greige_id')->widget(Select2::class, [
                        'initValueText' => $greigeLbl, // set the initial display text
                        'options' => ['placeholder' => 'Cari Greige...'],
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
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ]
                    ])->label('Pilih Greige');
                    ?>

                    <?php
                    $handling = null;
                    if(!empty($model->greige_id)){
                        $handling = MstHandling::find()->where(['greige_id' => $model->greige_id])->asArray()->all();
                    }
                    $data = !empty($handling) ? ArrayHelper::map($handling, 'id', 'name') : null;
                    echo $form->field($model, 'handling_id')->widget(DepDrop::classname(), [
                        'type'=>DepDrop::TYPE_SELECT2,
                        'data'=>$data,
                        'options'=>['placeholder'=>'Select ...'],
                        'select2Options'=>[
                            'pluginOptions'=>[
                                'allowClear'=>true
                            ]
                        ],
                        'pluginOptions' => [
                            'depends' => ['trnordercelup-greige_id'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['/dep-drop/handling'])
                        ]
                    ]);
                    ?>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'qty', [
                                'addon' => [
                                    'append' => ['content'=>'BATCH'],
                                ]
                            ])->textInput() ?>
                        </div>

                        <div class="col-md-8">
                            <?=$form->field($model, 'date')->widget(DatePicker::classname(), [
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
                    </div>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'color')->textInput() ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
