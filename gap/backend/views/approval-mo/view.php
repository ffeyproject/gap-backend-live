<?php
use common\models\ar\TrnMo;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnMo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Approval Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$formatter = Yii::$app->formatter;
$scGreige = $model->scGreige;
$sc = $scGreige->sc;
?>
    <div class="trn-mo-view">
        <p>
            <?php
            /*Html::a('Setujui', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'approveMo(event);',
                'title' => 'Approve SC: '.$model->id
            ]);*/?>
            <?=Html::a('Setujui', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'approveMo(event);',
                'title' => 'Approve MO: '.$model->id
            ]);?>
            <?=Html::a('Tolak', ['reject', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'onclick' => 'rejectMo(event);',
                'title' => 'Reject MO: '.$model->id
            ]);?>
        </p>
    </div>

    <iframe src="<?=Url::to(['/trn-mo/print-mo', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => Json::decode($model->reject_notes),
    'pagination' => false,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'toolbar' => false,
    'panel' => [
        'heading' => '<strong>Catatan Penolakan</strong>',
        'type' => GridView::TYPE_DEFAULT,
        'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'date_time',
        'note'
    ],
]);
?>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Stock Grige Didalam Group "<?=$scGreige->greigeGroup->nama_kain?>"</h3>
            <div class="box-tools pull-right">
                <span class="label label-warning"></span>
            </div>
        </div>
        <div class="box-body">
            <p>
                Menampilkan stok greige didalam group "<?=$scGreige->greigeGroup->nama_kain?>" di semua gudang, untuk membantu anda menentukan gudang greige untuk MO ini.
            </p>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">NO</th>
                    <th rowspan="2" style="vertical-align: middle;">GREIGE</th>
                    <th colspan="3" class="text-center">FRESH</th>
                    <th colspan="3" class="text-center">WIP</th>
                    <th colspan="3" class="text-center">PFP</th>
                    <th colspan="3" class="text-center">EX FINISH</th>
                </tr>
                <tr>
                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $scGreige->greigeGroup->mstGreiges as $i=>$greige):?>
                    <?php
                    $stockFresh = $greige->stock;
                    $bookedFresh = $greige->booked;
                    $availableFresh = $stockFresh - $bookedFresh;

                    $stockWip = $greige->stock_wip;
                    $bookedWip = $greige->booked_wip;
                    $availableWip = $stockWip - $bookedWip;

                    $stockPfp = $greige->stock_pfp;
                    $bookedPfp = $greige->booked_pfp;
                    $availablePfp = $stockPfp - $bookedPfp;

                    $stockEf = $greige->stock_ef;
                    $bookedEf = $greige->booked_ef;
                    $availableEf = $stockEf - $bookedEf;
                    ?>
                    <tr>
                        <td><?=$i+1?></td>
                        <td><?=$greige->nama_kain?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockFresh)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedFresh)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableFresh)?></td>

                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockWip)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedWip)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableWip)?></td>

                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockPfp)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedPfp)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($availablePfp)?></td>

                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockEf)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedEf)?></td>
                        <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableEf)?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="box-footer"></div>
    </div>

<?php
$JGForm = [
    '<form action="" class="formName">',
    '<div class="form-group">',
    '<label>Tentukan Jenis Gudang:</label>',
    '<select class="JGudang form-control">',
];

$strOpt = ['<option value="">--Pilih--</option>'];
if($model->process == TrnScGreige::PROCESS_PRINTING){
    $strOpt[] = '<option value="'.TrnStockGreige::JG_PFP.'">'.TrnStockGreige::jenisGudangOptions()[TrnStockGreige::JG_PFP].'</option>';
}else{
    foreach ( TrnStockGreige::jenisGudangOptions() as $key=>$jenisGudangOption) {
        $strOpt[] = '<option value="'.$key.'">'.$jenisGudangOption.'</option>';
    }
}

$JGForm[] = implode(' ', $strOpt);
$JGForm[] = '</select><div class="err-block text-danger"></div></div></form>';

$gudangOptions = implode(' ', $JGForm);
$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
var gudangOptions = '{$gudangOptions}';
//console.log(gudangOptions);

JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END);