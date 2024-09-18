<?php
use backend\components\Converter;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnScGreige;
use kartik\daterange\DateRangePicker;
use kartik\dialog\Dialog;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\AnalisaPengirimanProduksi */
/* @var $data array */
/* @var $form ActiveForm */

$this->title = 'Analisa Pengiriman Produksi';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-view">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Filter Form</strong></div>

        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'action' => ['analisa-pengiriman-produksi'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'no_kirim') ?>

                    <?=$form->field($searchModel, 'tgl_kirim', [
                        'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                        'options'=>['class'=>'drp-container mb-2']
                    ])->widget(DateRangePicker::classname(), [
                        'useWithAddon'=>true,
                        //'readOnly' => true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'Y-m-d',
                                'separator'=>' to ',
                            ],
                            //'opens'=>'left'
                        ]
                    ])?>

                    <?php
                    $customer = $searchModel->wo_id === null ? '' : $searchModel->woNo;
                    echo $form->field($searchModel, "wo_id")->widget(Select2::class, [
                        'initValueText' => $customer, // set the initial display text
                        'options' => ['placeholder' => 'Cari WO ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-wo-only-by-no']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                            'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                        ],
                    ])?>

                    <?php
                    $customer = $searchModel->buyer_id === null ? '' : $searchModel->buyerName;
                    echo $form->field($searchModel, "buyer_id")->widget(Select2::class, [
                        'initValueText' => $customer, // set the initial display text
                        'options' => ['placeholder' => 'Cari buyer ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/customer-no-and-name-search']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                            'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                        ],
                    ])?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($searchModel, 'no_lot') ?>

                    <?= $form->field($searchModel, 'motif') ?>

                    <?= $form->field($searchModel, 'design') ?>

                    <?= $form->field($searchModel, 'kombinasi') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($searchModel, 'piece_length') ?>

                    <?= $form->field($searchModel, 'jenis_order')->widget(Select2::classname(), [
                        'data' => \backend\models\search\AnalisaPengirimanProduksi::jenisOrderOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Data</strong>

            <div class="box-tools pull-right">
                <div>
                    Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11">
                    <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-sm', 'onclick'=>'printDiv("PrintTableData")'])?>
                </div>
            </div>
        </div>

        <div class="panel-body" id="PrintTableData">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="width: 40%; text-align: center">
                        <strong>PT. GAJAH ANGKASA PERKASA</strong><br>
                        ANALISA PENGIRIMAN PRODUKSI<br>
                        Tanggal: <?=Yii::$app->formatter->asDate($searchModel->fromDate)?> s/d <?=Yii::$app->formatter->asDate($searchModel->toDate)?><br>
                        (Dalam Satuan Yard)
                    </td>
                    <td style="width: 30%;"></td>
                </tr>
            </table>

            <p>&nbsp;</p>

            <table style="width: 100%;">
                <thead>
                <tr>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: left;">BUYER</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: left;">NO. DO</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: left;">DESIGN</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: left;">MOTIF</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: left;">JENIS</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">GRADE A</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">GRADE B</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">GRADE C</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">P / K</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">CONTOH</th>
                    <th style="border-top: 1px solid; border-bottom: 1px solid; text-align: right;">TOTAL</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['data'] as $datum):?>
                    <?php foreach ($datum['dos'] as $dos):?>
                        <?php foreach ($dos['designs'] as $design):?>
                            <tr>
                                <td><?=$datum['cust_no']?></td>
                                <td><?=$dos['no_do']?></td>
                                <td><?=$design['design']?></td>
                                <td><?=$dos['motif']?></td>
                                <td><?=$dos['jenis']?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['grade_a'])?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['grade_b'])?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['grade_c'])?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['grade_pk'])?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['contoh'])?></td>
                                <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($design['total'])?></td>
                            </tr>
                        <?php endforeach;?>
                        <tr>
                            <th></th>
                            <th style="border-top: 1px solid;">Total Per No. DO '<?=$dos['no_do']?>'</th>
                            <th style="border-top: 1px solid;"></th>
                            <th style="border-top: 1px solid;"></th>
                            <th style="border-top: 1px solid;"></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['grade_a'])?></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['grade_b'])?></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['grade_c'])?></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['grade_pk'])?></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['contoh'])?></th>
                            <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($dos['total'])?></th>
                        </tr>
                    <?php endforeach;?>
                    <tr>
                        <th style="border-top: 1px solid;">Total Per Buyer '<?=$datum['cust_no']?>'</th>
                        <th style="border-top: 1px solid;"></th>
                        <th style="border-top: 1px solid;"></th>
                        <th style="border-top: 1px solid;"></th>
                        <th style="border-top: 1px solid;"></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['grade_a'])?></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['grade_b'])?></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['grade_c'])?></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['grade_pk'])?></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['contoh'])?></th>
                        <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($datum['total'])?></th>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <th style="border-top: 1px solid;">GRAND TOTAL</th>
                    <th style="border-top: 1px solid;"></th>
                    <th style="border-top: 1px solid;"></th>
                    <th style="border-top: 1px solid;"></th>
                    <th style="border-top: 1px solid;"></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['grade_a'])?></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['grade_b'])?></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['grade_c'])?></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['grade_pk'])?></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['contoh'])?></th>
                    <th style="border-top: 1px solid; text-align: right;"><?=Yii::$app->formatter->asDecimal($data['grand_total']['total'])?></th>
                </tr>
                </tbody>
            </table>

            <?php //\yii\helpers\BaseVarDumper::dump($data, 10, true);?>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/analisa-pengiriman-produksi.js'), $this::POS_END);