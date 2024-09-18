<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyer */
/* @var $formatter \yii\i18n\Formatter*/
/* @var $dataStocks array*/
/* @var $dataItems array*/
?>

<div class="row">
    <div class="col-md-8">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Stock Tersedia</h3>
                <div class="box-tools pull-right">
                    <span class="label label-primary">-</span>
                </div>
            </div>
            <div class="box-body">
                <table id="TableStock" class="display select" style="width:100%">
                    <thead>
                    <tr>
                        <th><input name="select_all" value="1" type="checkbox"></th>
                        <th>Jenis Gudang</th>
                        <th>Source</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="box-footer">
                <?=Html::a('Ambil', ['ambil-stock', 'id'=>$model->id], [
                    'class' => 'btn btn-success',
                    'onclick' => 'ambilStock(event);',
                    'title' => 'Ambil Stock Terpilih'
                ])?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Stock Akan Dikirim</h3>
                <div class="box-tools pull-right">
                    <span class="label label-primary">-</span>
                </div>
            </div>
            <div class="box-body">
                <table id="TableItem" class="display select" style="width:100%">
                    <thead>
                    <tr>
                        <th><input name="select_all" value="1" type="checkbox"></th>
                        <th>Qty</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="box-footer">
                <?=Html::a('Kembalikan', ['kembalikan-stock', 'id'=>$model->id], [
                    'class' => 'btn btn-danger',
                    'onclick' => 'kembalikanStock(event);',
                    'title' => 'Kembalikan Stock Terpilih'
                ])?>
            </div>
        </div>
    </div>
</div>

<?php
$dataStocksStr = Json::encode($dataStocks);
$dataItemssStr = Json::encode($dataItems);
$js = <<<JS
var dataStocks = {$dataStocksStr};
var dataItems = {$dataItemssStr};
//console.log(dataStocks);
JS;

$this->registerJs($js.$this->renderFile(__DIR__.'/js/view.js'), View::POS_END);
$this->registerCss('table.dataTable.select tbody tr,
table.dataTable thead th:first-child {
  cursor: pointer;
}');