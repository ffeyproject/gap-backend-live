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

        <div class="row">
            <div class="col-md-6">
                <?php
                //echo '<label class="control-label">Greige Group</label>';
                echo Select2::widget([
                    'name' => 'greige-group',
                    'id' => 'Select2Greige',
                    'options' => ['placeholder' => 'Cari greige ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/ajax/greige-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(greigeGroup) { return greigeGroup.text; }'),
                        'templateSelection' => new JsExpression('function (greigeGroup) { return greigeGroup.text; }'),
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function(e) {
                    //console.log($(this).val());
                    greigeId = $(this).val();
                 }",
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-4">
                <?php
                //echo '<label class="control-label">Greige Group</label>';
                echo Select2::widget([
                    'name' => 'greige-grade',
                    'id' => 'Select2GreigeGrade',
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => 'Pilih Grade ...'],
                    'pluginEvents' => [
                        "select2:select" => "function(e) {
                    //console.log($(this).val());
                    greigeGrade = $(this).val();
                 }",
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-2"><?=Html::button('Mix', ['class' => 'btn btn-info', 'id'=>'BtnMixQuality'])?></div>
        </div>
    </div>
</div>
