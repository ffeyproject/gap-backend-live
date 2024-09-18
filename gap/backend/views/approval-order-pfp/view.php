<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Persetujuan Order PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-order-pfp-view">
    <p>
        <?php if($model->status == $model::STATUS_POSTED):?>
        <?= Html::a('Setujui', ['approve', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Anda yakin akan menyetujui dokumen ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?=Html::a('Tolak', ['reject', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'onclick' => 'rejectOrderPfp(event);',
            'title' => 'Tolak Order PFP: '.$model->id
        ]);?>
        <?php endif; ?>
    </p>

    <?= $this->render('/trn-order-pfp/_view-header', ['model'=>$model])?>

    <?= $this->render('/trn-order-pfp/_view-content', ['model'=>$model])?>
</div>

<?php
$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END);
