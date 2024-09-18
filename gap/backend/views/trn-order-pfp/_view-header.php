<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-order-pfp-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Posting', ['posting', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Anda yakin akan memposting item ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </p>
</div>
