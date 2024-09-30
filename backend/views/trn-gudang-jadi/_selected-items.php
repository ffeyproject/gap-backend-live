<?php
use common\models\ar\TrnStockGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Items</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body">
        <table id="ItemsTable" class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Jenis Gudang</th>
                <th>Marketing</th>
                <th>Buyer</th>
                <th>No. SC</th>
                <th>No. WO</th>
                <th>Color</th>
                <th>Source</th>
                <th>Source Ref.</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Grade</th>
                <th>Motif</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="box-footer">
        <p>
            <?=Html::a('Set Sebagai Siap Kirim', ['set-siap-kirim'], [
                'class' => 'btn btn-info',
                'onclick' => 'readyForSend(event);',
                'title' => 'Set Sebagai Siap Kirim'
            ])
            .' '.Html::a('Set Sebagai Stock', ['set-stock'], [
                'class' => 'btn btn-default',
                'onclick' => 'setAsStock(event);',
                'title' => 'Set Sebagai Stock'
            ])
            /*.' '.Html::a('Kirim', ['kirim'], [
                'class' => 'btn btn-default',
                'onclick' => 'readyForSend(event);',
                'title' => 'Kirim'
            ])*/
            .' '.Html::a('Mutasi Ke GD Ex Finish', ['mutasi-ex-finish'], [
                'class' => 'btn btn-default',
                'onclick' => 'mutasikanKeExFinish(event);',
                'title' => 'Mutasi Ke GD Ex Finish'
            ])?>
        </p>

        <hr>

        <div class="row">
            <div class="col-md-5">
                <label class="control-label">Pindah Gudang</label>
                <?=Select2::widget([
                    'name' => 'jenis_gudang_selection',
                    'id' => 'JenisGudangSelect',
                    'data' => \common\models\ar\TrnGudangJadi::jenisGudangOptions(),
                    'options' => [
                        'placeholder' => 'Pilih jenis gudang ...',
                        'allowClear' => true
                    ],
                    'addon' => [
                        'append' => [
                            'content' => Html::a('Pindahkan', ['pindah-gudang'], ['class'=>'btn btn-warning', 'onclick' => 'pindahGudang(event);']),
                            'asButton' => true
                        ]
                    ]
                ])?>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-5">
                <label class="control-label">Print Label Palet</label>
                <?=Select2::widget([
                    'name' => 'locs_code',
                    'id' => 'JenisLocationSelect',
                    'data' => \common\models\ar\TrnGudangJadi::getLocationAreas(),
                    'options' => [
                        'placeholder' => 'Pilih kode lokasi ...',
                        'allowClear' => true
                    ],
                    'addon' => [
                        'append' => [
                            'content' => Html::a('Print Label', ['print-label'], ['class'=>'btn btn-warning', 'onclick' => 'printLabel(event);']),
                            'asButton' => true
                        ]
                    ]
                ])?>
            </div>
            <div class="col-md-1"></div>
        </div>

    </div>
</div>
