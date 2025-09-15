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
                <th>Aksi</th>
                <th>ID</th>
                <th>Tanggal</th>
                <th>No. Document</th>
                <th>No. Lapak</th>
                <th>Greige</th>
                <th>Grade</th>
                <th>Lot Pakan</th>
                <th>Lot Lusi</th>
                <th>No. Mc. Weaving</th>
                <th>Qty</th>
                <th>Asal Greige</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pull-right">
            <?=Html::button('Set To Out', ['class' => 'btn btn-danger', 'id'=>'BtnSetOut'])?>
        </div>
</div>
