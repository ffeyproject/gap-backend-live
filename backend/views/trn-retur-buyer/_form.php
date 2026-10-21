<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnReturBuyerItem;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var  $modelsItem TrnReturBuyerItem[]*/
/* @var $form ActiveForm */
?>

<div class="trn-retur-buyer-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if($model->isNewRecord){
                                // WO dipilih per item, satu dokumen retur dibuat untuk setiap WO
                                echo '<div class="form-group"><label class="control-label">Nomor WO</label><p class="form-control-static text-muted">Dipilih per item (bisa lebih dari satu WO)</p></div>';
                            }else{
                                echo '<div class="form-group"><label class="control-label" for="nomorWo">Nomor WO</label><input value="'.$model->wo->no.'" type="text" id="nomorWo" class="form-control" disabled></div>';
                            }
                            ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'jenis_gudang')->widget(Select2::classname(), [
                                'data' => \common\models\ar\TrnGudangJadi::jenisGudangOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-6">
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($model, 'date_document')->widget(\kartik\widgets\DatePicker::classname(), [
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
                        <div class="col-md-6">
                            <?= $form->field($model, 'no_document')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'penanggungjawab')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'nama_qc')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Item Retur</h3>
                    <p class="help-block" style="margin-bottom: 0">
                        <?php if($model->isNewRecord):?>
                        Bisa berisi banyak WO dari customer yang sama, saat disimpan dibuat 1 dokumen retur per WO dengan nomor dokumen yang sama.
                        Scan tanpa memilih WO akan mengisi WO otomatis.<br>
                        <?php endif;?>
                        Jika label roll masih ada, scan QR / ketik ID roll lalu tekan Enter.
                        Jika label hilang, pilih surat jalan lalu roll yang cocok (lihat qty &amp; grade).
                        Surat jalan dan roll boleh dibiarkan "Tidak diketahui", qty tetap dibatasi total kiriman WO.
                    </p>
                </div>
                <div class="box-body">
                    <?=$this->render('child/_form_items', ['form'=>$form, 'model'=>$model, 'modelsItem'=>$modelsItem])?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
