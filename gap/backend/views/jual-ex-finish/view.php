<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinish */
/* @var $dataProvider yii\data\ArrayDataProvider*/

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jual Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="jual-ex-finish-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-sm-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no_urut',
                            'no',
                            'jenisGudangName',
                            'customerName',
                            'gradeName',
                            'harga:decimal',
                            'ongkirName',
                            'pembayaran',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'tanggal_pengiriman:date',
                            'komisi',
                            'jenisOrderName',
                            'is_resmi:boolean',
                            'keterangan:ntext',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Items</h3>
            <div class="box-tools pull-right">
                <span class="label label-warning"></span>
            </div>
        </div>
        <div class="box-body">
            <?=\yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    //'greige_id',
                    [
                        'label' => 'Motif',
                        'value' => function($data){
                            return \common\models\ar\MstGreige::findOne($data['greige_id'])->nama_kain;
                        }
                    ],
                    //'grade',
                    [
                        'label' => 'Grade',
                        'value' => function($data){
                            return TrnStockGreige::gradeOptions()[$data['grade']];
                        }
                    ],
                    'qty:decimal',
                    //'unit',
                    [
                        'label' => 'Unit',
                        'value' => function($data){
                            return MstGreigeGroup::unitOptions()[$data['unit']];
                        }
                    ],
                ]
            ])?>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <!--<h3 class="box-title">Default Box Example</h3>-->
            <div class="box-tools pull-right">
                Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11">
                <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-sm', 'onclick'=>'printDiv("SuratMemo")'])?>
            </div>
        </div>

        <div class="box-body">
            <div id="SuratMemo">
                <table class="table">
                    <tr>
                        <td style="width: 18%;"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 30%;"></td>
                        <td style="width: 10%;"></td>
                        <td style="width: 20%;"></td>
                        <td style="width: 20%;"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="vertical-align: top; font-size: 150%;"><strong>MEMO PEMESANAN BARANG STOCK</strong></td>
                        <td rowspan="3"></td>
                        <td style="border-top: 1px solid black; border-left: 1px solid black;">
                            <p>No. Memo</p>
                            <p><?=$model->no?></p>
                        </td>
                        <td style="border-top: 1px solid black; border-right: 1px solid black;">
                            <p>Tanggal Memo</p>
                            <p><?=Yii::$app->formatter->asDate($model->tanggal_pengiriman)?></p>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">Customer</td>
                        <td rowspan="2">:</td>
                        <td style="border-top: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;"><?=$model->customer->name?></td>
                        <td style="border-left: 1px solid black;">No. PO:<br><?=$model->no_po?></td>
                        <td style=" border-right: 1px solid black;">Pembayaran: <br><?=$model->pembayaran?></td>
                    </tr>
                    <tr>
                        <td style="border-right: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black;"><?=$model->customer->address?></td>
                        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">Ongkos Kirim:<br><?=$model->ongkirName?></td>
                    </tr>
                </table>

                <p>&nbsp;</p>

                <table class="table table-bordered">
                    <tr>
                        <th>No</th>
                        <th>Jenis Barang</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Ket. Barang</th>
                    </tr>
                    <?php $total = 0; foreach ($model->jualExFinishItems as $i=>$item):?>
                    <tr>
                        <td><?=$i+1?></td>
                        <td><?=$model->jenisOrderName?></td>
                        <td><?=\common\models\ar\MstGreige::findOne($item['greige_id'])->nama_kain?></td>
                        <td><?=Yii::$app->formatter->asDecimal($item['qty'])?></td>
                        <td><?=MstGreigeGroup::unitOptions()[$item['unit']]?></td>
                        <td>Ex WO: <?=$item['no_wo']?></td>
                    </tr>
                    <?php $total += $item['qty']; endforeach;?>
                    <tr>
                        <td colspan="3">Grand Total</td>
                        <td><?=Yii::$app->formatter->asDecimal($total)?></td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                <p>&nbsp;</p>

                <table class="table">
                    <tr>
                        <td style="width: 20%;">
                            Dibuat Oleh,
                            <br><br><br><br><br>
                            --------------------<br>
                            Tgl:
                        </td>
                        <td style="width: 20%;">
                            Marketing,
                            <br><br><br><br><br>
                            --------------------<br>
                            Tgl:
                        </td>
                        <td style="width: 20%;">
                            Disetujui Oleh,
                            <br><br><br><br><br>
                            --------------------<br>
                            Tgl:
                        </td>
                        <td style="width: 40%;">
                            <strong>Remarks</strong>
                            <div class="bordered" style="width: 100%; height: 100%; padding: 1px;">
                                <?=$model->keterangan?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <div class="col-md-6">

                </div>

                <div class="col-md-6"></div>
            </div>
        </div>
    </div>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), $this::POS_END);

