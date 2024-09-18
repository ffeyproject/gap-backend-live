<?php
use backend\components\ajax_modal\AjaxModal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Kartu Proses Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="kartu-proses-celup-view">

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

            <?=Html::a('Ganti Warna', ['ganti-warna', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'onclick' => 'gantiWarna(event);',
                'title' => 'Ganti Warna Kartu Proses: '.$model->id
            ]);?>
        <?php endif;?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-celup/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-celup/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-celup/child/persetujuan', ['model' => $model]);?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesCelupModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END);