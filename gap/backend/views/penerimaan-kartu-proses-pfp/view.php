<?php
use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstHandling;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Kartu Proses Pfp', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$panjangTotal = $model->getTrnKartuProsesPfpItems()->sum('panjang_m');

/* @var $handling MstHandling*/
$handling = MstHandling::find()->where(['greige_id'=>$model->greige_id, 'name'=>$model->handling])->one();

$beratPeneerimaan = $panjangTotal;
if($handling !== null && $handling->berat_persiapan>0){
    $beratPeneerimaan = $beratPeneerimaan * $handling->berat_persiapan;
}
?>
<div class="kartu-proses-pfp-view">

    <p>
        <?php if($model->status == $model::STATUS_POSTED):?>
            <?=Html::a('Terima', ['terima', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'terimaKartuProses(event);',
                'title' => 'Terima Kartu Proses: '.$model->id
            ]);?>

            <?=Html::a('Tolak', ['tolak', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'onclick' => 'tolakKartuProses(event);',
                'title' => 'Tolak Kartu Proses: '.$model->id
            ]);?>
        <?php endif;?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/persetujuan', ['model' => $model]);?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesPfpModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

$this->registerJsVar('indexUrl', Url::to(['index']));
$this->registerJsVar('beratPeneerimaan', $beratPeneerimaan);
$js = $this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END);