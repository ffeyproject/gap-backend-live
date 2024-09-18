<?php
use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstHandling;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Kartu Proses Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$panjangTotal = $model->getTrnKartuProsesDyeingItems()->sum('panjang_m');
$panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;

/* @var $handling MstHandling*/
$handling = MstHandling::find()->where(['greige_id'=>$model->wo->greige_id, 'name'=>$model->handling])->one();

$beratPeneerimaan = $panjangTotal;
if($handling !== null && $handling->berat_persiapan>0){
    $beratPeneerimaan = $beratPeneerimaan * $handling->berat_persiapan;
}
?>
<div class="kartu-proses-dyeing-view">

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

            <?=Html::a('Ganti WO', ['ganti-wo', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'onclick' => 'gantiWo(event);',
                'title' => 'Ganti WO Kartu Proses: '.$model->id
            ]);?>

            <?=Html::a('Ganti Warna', ['ganti-warna', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'onclick' => 'gantiWarna(event);',
                'title' => 'Ganti Warna Kartu Proses: '.$model->id
            ]);?>
        <?php endif;?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/persetujuan', ['model' => $model]);?>
</div>

<?php
$warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
$indexUrl = \yii\helpers\Url::to(['index']);

$this->registerJsVar('indexUrl', $indexUrl);
$this->registerJsVar('warnaList', $warnaList);
$this->registerJsVar('beratPeneerimaan', $beratPeneerimaan);
$this->registerJs($this->renderFile(Yii::$app->controller->viewPath.'/js/view.js'), $this::POS_END);