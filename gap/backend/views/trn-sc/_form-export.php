<?php
use backend\models\form\TrnScExportForm;
use backend\modules\user\models\User;
use common\models\ar\TrnSc;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model TrnScExportForm */
/* @var $form ActiveForm */
?>

<div class="trn-sc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=$model->getHiddenFormTokenField()?>

    <div class="row">
        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?php
                    $ajaxUrl = Url::to(['ajax/customer-search']);
                    $customer = empty($model->cust_id) ? '' : $model->cust->name.' ('.$model->cust->cust_no.')';
                    echo $form->field($model, "cust_id")->widget(Select2::class, [
                        'initValueText' => $customer, // set the initial display text
                        'options' => ['placeholder' => 'Cari buyer ...'],
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
                        ],
                    ])?>

                    <div class="row">
                        <div class="col-md-4">
                            <?=$form->field($model, 'currency')->widget(Select2::class, [
                                'data' => $model::currencyOptions(),
                                'options' => ['placeholder' => 'Mata uang ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])?>
                        </div>

                        <div class="col-md-8">
                            <?=$form->field($model, 'jenis_order')->widget(Select2::class, [
                                'data' => TrnSc::jenisOrderOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])?>
                        </div>
                    </div>

                    <?=$form->field($model, 'ongkos_angkut')->widget(Select2::class, [
                        'data' => TrnSc::ongkosAngkutOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])?>

                    <?= $form->field($model, 'pmt_method')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'jet_black')->checkbox() ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'destination')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'pmt_term', [
                        'addon' => [
                            'append' => [
                                'content' => 'Hari'
                            ]
                        ]
                    ])->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'due_date')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ])?>

                    <?=$form->field($model, 'delivery_date')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ])?>

                    <?= $form->field($model, 'disc_grade_b', [
                        'addon' => [
                            'append' => [
                                'content' => '%'
                            ]
                        ]
                    ])->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'no_po')->textInput(['maxlength' => true]) ?>

                    <?=$form->field($model, 'direktur_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(Yii::$app->params['rbac_roles']['dir_marketing']),
                    ])?>

                    <?=$form->field($model, 'manager_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(Yii::$app->params['rbac_roles']['mgr_marketing']),
                    ])?>

                    <?=$form->field($model, 'marketing_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles('Marketing'),
                    ])?>

                    <?= $form->field($model, 'disc_piece_kecil', [
                        'addon' => [
                            'append' => [
                                'content' => '%'
                            ]
                        ]
                    ])->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <?=$form->field($model, 'packing')->widget(TinyMce::class, [
                'options' => ['rows' => 6],
                'language' => 'id',
                'clientOptions' => [
                    'menubar' => false,
                    'plugins' => [
                        "lists",
                    ],
                    'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
                ]
            ])?>

            <?=$form->field($model, 'note')->widget(TinyMce::class, [
                'options' => ['rows' => 6],
                'language' => 'id',
                'clientOptions' => [
                    'menubar' => false,
                    'plugins' => [
                        "lists",
                    ],
                    'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
                ]
            ])?>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'bank_acct_id')->widget(Select2::class, [
                        'data' => \common\models\ar\MstBankAccount::optionList(),
                        'options' => ['placeholder' => 'Pilih akun Bank ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])?>
                </div>

                <div class="col-md-3"><?= $form->field($model, 'consignee_name')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-3"><?= $form->field($model, 'notify_party')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-3"><?= $form->field($model, 'buyer_name_in_invoice')->textInput(['maxlength' => true]) ?></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->render('js/form_export.js'), View::POS_END);