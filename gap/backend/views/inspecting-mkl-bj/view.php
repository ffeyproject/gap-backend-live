<?php

use backend\components\Converter;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\MstGreigeGroup;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */

$this->title = 'Inspecting Makloon Dan Barang Jadi - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Makloon Dan Barang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$perBatch = $model->wo->greige->group->qty_per_batch;
$formatter = Yii::$app->formatter;

$totalQtyGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
];
$totalPiecesGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
];
$totalRollGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
];
$joinPieces = [
    InspectingMklBjItems::GRADE_A => [],
    InspectingMklBjItems::GRADE_B => [],
    InspectingMklBjItems::GRADE_C => [],
    InspectingMklBjItems::GRADE_PK => [],
    InspectingMklBjItems::GRADE_SAMPLE => [],
];
?>
<div class="inspecting-mkl-bj-view">

    <p>
        <?php
        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]).' ';
                echo Html::a('Posting', ['posting', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
        }

        echo ' '.Html::a('Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default']);
        ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no',
                            'woNo',
                            'colorName',
                            'tgl_inspeksi:date',
                            'tgl_kirim:date',
                            'no_lot',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'jenisName',
                            'satuanName',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'statusName',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!--Items-->
    <div class="box">
        <div class="box-header with-border">
            <span><strong>Packing List</strong></span>

            <div class="box-tools pull-right">
                <span class="label label-info">
                    <?='<strong>Greige: '.$model->wo->greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($perBatch).' '.$model->satuanName.'</strong>'?>
                </span>
            </div>
        </div>

        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No. Packing</th>
                    <th>Grade A</th>
                    <th>Grade B</th>
                    <th>Grade C</th>
                    <th>Piece Kecil</th>
                    <th>Contoh</th>
                    <th>Keterangan</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->getItems()->orderBy('id')->all() as $index=>$item):?>
                    <?php
                    if($item['qty'] > 0){
                        // akumulasi hanya berlaku jika qty > 0
                        $totalQtyGrade[$item['grade']] += $item['qty'];
                        $totalPiecesGrade[$item['grade']] ++;

                        if(empty($item['join_piece'])){
                            $totalRollGrade[$item['grade']] ++;
                        }else{
                            if(!in_array($item['join_piece'], $joinPieces[$item['grade']])){
                                $totalRollGrade[$item['grade']] ++;
                                $joinPieces[$item['grade']][] = $item['join_piece'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><?=($index+1).$item['join_piece']?></td>
                        <td>
                            <?php
                            if($item['grade'] === InspectingMklBjItems::GRADE_A){
                                echo $formatter->asDecimal($item['qty']);
                            }else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($item['grade'] === InspectingMklBjItems::GRADE_B){
                                echo $formatter->asDecimal($item['qty']);
                            }else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($item['grade'] === InspectingMklBjItems::GRADE_C){
                                echo $formatter->asDecimal($item['qty']);
                            }else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($item['grade'] === InspectingMklBjItems::GRADE_PK){
                                echo $formatter->asDecimal($item['qty']);
                            }else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($item['grade'] === InspectingMklBjItems::GRADE_SAMPLE){
                                echo $formatter->asDecimal($item['qty']);
                            }else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td><?=$item['note']?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>

        <div class="box-footer with-border">
            <?php
            $totalPieces = $totalPiecesGrade[InspectingMklBjItems::GRADE_A] + $totalPiecesGrade[InspectingMklBjItems::GRADE_B] + $totalPiecesGrade[InspectingMklBjItems::GRADE_C] + $totalPiecesGrade[InspectingMklBjItems::GRADE_PK] + $totalPiecesGrade[InspectingMklBjItems::GRADE_SAMPLE];;
            $totalQty = $totalQtyGrade[InspectingMklBjItems::GRADE_A] + $totalQtyGrade[InspectingMklBjItems::GRADE_B] + $totalQtyGrade[InspectingMklBjItems::GRADE_C] + $totalQtyGrade[InspectingMklBjItems::GRADE_PK] + $totalQtyGrade[InspectingMklBjItems::GRADE_SAMPLE];
            $totalRoll = $totalRollGrade[InspectingMklBjItems::GRADE_A] + $totalRollGrade[InspectingMklBjItems::GRADE_B] + $totalRollGrade[InspectingMklBjItems::GRADE_C] + $totalRollGrade[InspectingMklBjItems::GRADE_PK] + $totalRollGrade[InspectingMklBjItems::GRADE_SAMPLE];

            if($model->satuan == MstGreigeGroup::UNIT_YARD){
                $totalM = Converter::yardToMeter($totalQty);
            }else{
                $totalM = $totalQty;
            }
            $susutM = $perBatch - $totalM;
            $susutPcnt = (($perBatch-$totalM) / $perBatch) * 100;
            ?>

            <p><strong>Total: <?=Yii::$app->formatter->asDecimal($totalM)?> M</strong></p>
            <p><strong>Susut: <?=Yii::$app->formatter->asDecimal($susutM)?> M (<?=$susutPcnt?>%)</strong></p>
        </div>
    </div>
    <!--Items-->
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Total</th>
                        <th>Total Pieces</th>
                        <th>Total Roll</th>
                        <th>Total Ukuran</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>Total Grade A</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Grade B</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Grade C</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Piece Kecil</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Contoh</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <th><?=$formatter->asDecimal($totalPieces)?></th>
                        <th><?=$formatter->asDecimal($totalRoll)?></th>
                        <th><?=$formatter->asDecimal($totalQty)?></th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <span><strong>Catatan Penolakan</strong></span>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($model->delivery_reject_note !== null){
                        $notes = \yii\helpers\Json::decode($model->delivery_reject_note);
                        foreach ($notes as $note) {
                            echo Html::tag(
                                'tr',
                                Html::tag('td', $note['date_time'])
                                .Html::tag('td', $note['note'])
                            );
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>