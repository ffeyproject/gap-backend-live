<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
?>

<div class="modal fade" id="modal-save-daily-stock" tabindex="-1" role="dialog" aria-labelledby="modalSaveDailyStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="<?= Url::to(['/trn-stock-greige-daily/save-snapshot']) ?>" method="post" id="form-save-daily-stock">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="returnUrl" value="<?= Yii::$app->request->url ?>">

                <div class="modal-header bg-primary text-white" style="background-color: #337ab7; color: #fff;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #fff;">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalSaveDailyStockLabel">
                        <i class="glyphicon glyphicon-camera"></i> Simpan Snapshot Stock Harian
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="glyphicon glyphicon-info-sign"></i> 
                        Sistem akan menghitung total stock (meter & roll) dari <strong>Gudang Fresh (Status Valid)</strong> untuk setiap motif greige dan menyimpannya sebagai catatan per hari.
                    </div>

                    <div class="form-group">
                        <label for="snapshot-date" class="control-label">Pilih Tanggal Snapshot:</label>
                        <?= DatePicker::widget([
                            'name' => 'date',
                            'value' => date('Y-m-d'),
                            'options' => ['placeholder' => 'Pilih tanggal...', 'id' => 'snapshot-date', 'required' => true],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                                'todayHighlight' => true,
                            ]
                        ]); ?>
                        <div id="snapshot-day-preview" style="margin-top: 6px; font-weight: bold; color: #2980b9;">
                            <i class="glyphicon glyphicon-time"></i> Hari: <?= \common\models\ar\TrnStockGreigeDaily::getIndonesianDayName(date('Y-m-d')) ?>, <?= Yii::$app->formatter->asDate(date('Y-m-d'), 'php:d M Y') ?>
                        </div>
                        <p class="help-block text-muted" style="margin-top: 4px;">Jika tanggal yang dipilih sudah pernah disimpan sebelumnya, data akan diperbarui (update) dengan data stock terkini.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="glyphicon glyphicon-remove"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="btn-submit-snapshot">
                        <i class="glyphicon glyphicon-floppy-disk"></i> Simpan Snapshot Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
