<?php

use backend\modules\user\models\User;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */
/* @var $form ActiveForm */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */
?>

<div class="trn-mo-form">
    <?=$form->errorSummary($model)?>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?=$form->field($model, 'est_produksi')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true
                        ],
                    ])?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($model, 'est_packing')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',//'mm/dd/yyyy'
                            'todayHighlight' => true
                        ],
                    ])?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($model, 'target_shipment')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',//'mm/dd/yyyy'
                            'todayHighlight' => true
                        ],
                    ])?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div id="ReWoContainer" class="col-md-6">
                    <?= $form->field($model, 're_wo', [
                        'addon' => [
                            'append' => [
                                'content' => Html::button('Re WO', ['id' => 'CheckBtMoReWo', 'class'=>'btn btn-primary']),
                                'asButton' => true
                            ]
                        ]
                    ])->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'approval_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(Yii::$app->params['rbac_roles']['kabag_pmc']),
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'piece_length')->textInput(['maxlength' => true])->label('PL') ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'jet_black')->dropDownList(
                                [ 1 => 'Ya', 0 => 'Tidak'],
                                ['prompt' => 'Pilih ...']
                            ) ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'heat_cut')->dropDownList(
                                [ 1 => 'Ya', 0 => 'Tidak'],
                                ['prompt' => 'Pilih ...']
                            ) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'article')->textInput() ?>

                    <?= $form->field($model, 'hanger')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'joint')->dropDownList(
                                [ 1 => 'Ya', 0 => 'Tidak'],
                                ['prompt' => 'Pilih ...']
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'joint_qty', [
                                'addon' => [
                                    'append' => [
                                        'content' => MstGreigeGroup::unitOptions()[$scGreige->greigeGroup->unit]
                                    ]
                                ]
                            ])->textInput() ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'selvedge_stamping')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'selvedge_continues')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'side_band')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'folder')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'arsip')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'handling')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'album')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'packing_method')->dropDownList(
                        $model::packingMethodOptions(),
                        ['prompt' => 'Pilih ...']
                    )?>

                    <?= $form->field($model, 'shipping_method')->dropDownList(
                        $model::shippingMethodOptions(),
                        ['prompt' => 'Pilih ...']
                    )?>

                    <?= $form->field($model, 'shipping_sorting')->dropDownList(
                        $model::shippingSortingOptions(),
                        ['prompt' => 'Pilih ...']
                    )?>

                    <?= $form->field($model, 'plastic')->dropDownList(
                        $model::plasticOptions(),
                        ['prompt' => 'Pilih ...']
                    )?>

                    <?php
                        echo $form->field($model, 'persen_grading')->widget(Select2::class, [
                            'options' => ['placeholder' => 'Pilih ...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'data' => $model::persenGradingOptions(),
                        ]);
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?=$form->field($model, 'face_stamping')->widget(TinyMce::class, [
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

                <div class="col-md-6">
                    <?= $form->field($model, 'note')->widget(TinyMce::class, [
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
        </div>
    </div>
</div>
