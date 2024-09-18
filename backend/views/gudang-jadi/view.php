<?php
use common\models\ar\TrnInspecting;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */

$formatter = Yii::$app->formatter;

$this->title = 'Penerimaan Packing: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Packing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

if($model->kartu_process_dyeing_id !== null){
    $jenis = 'dyeing';
}else{
    $jenis = 'printing';
}

$inspectingItems = $model->getInspectingItems()->asArray()->all();

if($model->kartu_process_dyeing_id !== null){
    $kartuProses = $model->kartuProcessDyeing;
    $kartuProsesUrl = 'kartu-proses-dyeing';
    $isDyeing = true;
    $isPfp = false;
    $isPrinting = false;
    $isMaklon = false;
    $jenis = 'dyeing';

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}else if($model->kartu_process_printing_id !== null){
    $kartuProses = $model->kartuProcessPrinting;
    $kartuProsesUrl = 'kartu-proses-printing';
    $isDyeing = false;
    $isPfp = false;
    $isPrinting = true;
    $isMaklon = false;
    $jenis = 'printing';

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}else{
    $kartuProses = $model->memo_repair_id;
    $kartuProsesUrl = 'kartu-proses-maklon';
    $isDyeing = false;
    $isPfp = false;
    $isPrinting = false;
    $isMaklon = true;
    $jenis = 'maklon';

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}

/*$wo = $model->wo;
$greige = $wo->greige;
$mo = $model->mo;
$scGreigeGroup = $mo->scGreige;
$sc = $model->sc;
$cust = $sc->cust;*/
?>
<div class="inspecting-view">
    <p>
        <?php
        if($model->status == $model::STATUS_APPROVED){
            echo Html::a('Terima', ['terima', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to terima this item?',
                    'method' => 'post',
                ],
            ]);
            echo ' ';

            //sementara penolakan dimatikan karena belum perlu
            /*echo Html::a('Tolak', ['tolak', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'onclick' => 'rejectInspect(event);',
                'title' => 'Reject Inspecting: '.$model->id
            ]);

            $indexUrl = Url::to(['index']);
            $jsStr = 'var indexUrl = "'.$indexUrl.'";';
            $js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
            $this->registerJs($js, $this::POS_END);*/
        }
        ?>
    </p>

    <?=$this->render('/trn-inspecting/child/_header', ['model'=>$model, 'kartuProses'=>$kartuProses, 'kartuProsesUrl'=>$kartuProsesUrl, 'greige'=>$greige])?>

    <?=$this->render('/trn-inspecting/child/_items', ['model'=>$model, 'greige'=>$greige, 'inspectingItems'=>$inspectingItems, 'formatter'=>$formatter])?>

</div>

<?php
//sementara penolakan dimatikan karena belum perlu
/*$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END);*/