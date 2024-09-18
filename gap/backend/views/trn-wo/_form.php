<?php

use backend\modules\user\models\User;
use common\models\ar\MstGreige;
use common\models\ar\MstHandling;
use common\models\ar\MstPapperTube;
use common\models\ar\TrnSc;
use common\models\ar\TrnWo;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $form ActiveForm */
?>

<div class="trn-wo-form">
    <?=$this->render('_mo-info', ['mo' => $model->mo, 'sc'=>$model->sc, 'scGreige'=>$model->scGreige])?>

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->errorSummary($model)?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    $res =  MstGreige::find()->where([
                        'group_id' => $model->scGreige->greige_group_id,
                        'aktif'=>true
                    ])->orderBy('nama_kain')->all();
                    $list = ArrayHelper::map($res, 'id', function($data){
                        /* @var $data MstGreige*/
                        return $data->nama_kain." (Alias: {$data->alias})";
                    });
                    echo $form->field($model, 'greige_id')->widget(Select2::class, [
                        'data' => $list,
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>

                <div class="col-md-4">
                    <?php
                    $handling = null;
                    if(!empty($model->greige_id)){
                        $rows = (new Query())
                            ->select(['id', 'name', 'buyer_ids'])
                            ->from(MstHandling::tableName())
                            ->where(['greige_id' => $model->greige_id])
                            ->andWhere('string_to_array(buyer_ids, \',\') && array[:idBuyer]')
                            ->addParams([':idBuyer' => $model->sc->cust_id])
                            ->all()
                        ;

                        if(empty($rows)){
                            $rows = MstHandling::find()->where(['greige_id' => $model->greige_id])->asArray()->all();
                        }
                    }
                    $data = !empty($rows) ? ArrayHelper::map($rows, 'id', 'name') : null;
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
                            'depends' => ['trnwo-greige_id'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['/dep-drop/handling-by-cust', 'custID'=>$model->sc->cust_id])
                        ]
                    ]);
                    ?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($model, 'mengetahui_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(Yii::$app->params['rbac_roles']['kabag_pmc']),
                    ])?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?=$form->field($model, 'jenis_order')->widget(Select2::class, [
                        'data' => TrnSc::jenisOrderOptions(),
                        'options' => ['placeholder' => 'Pilih jenis order ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($model, 'papper_tube_id')->widget(Select2::class, [
                        'data' => MstPapperTube::optionList(),
                        'options' => ['placeholder' => 'Pilih papper Tube ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])?>
                </div>

                <div class="col-md-4"><?= $form->field($model, 'plastic_size')->textInput(['maxlength' => true]) ?></div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?=$form->field($model, 'tgl_kirim')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true
                        ],
                    ])?>
                </div>

                <div class="col-md-4"></div>

                <div class="col-md-4"></div>
            </div>

            <?=$form->field($model, 'shipping_mark')->widget(TinyMce::class, [
                'options' => ['rows' => 3],
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
                'options' => ['rows' => 3],
                'language' => 'id',
                'clientOptions' => [
                    'menubar' => false,
                    'plugins' => [
                        "lists",
                    ],
                    'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
                ]
            ])?>

            <?=$form->field($model, 'note_two')->widget(TinyMce::class, [
                'options' => ['rows' => 3],
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

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
