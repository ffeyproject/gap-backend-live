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
    <?= $this->render('_view-header', ['model'=>$model])?>

    <?= $this->render('_view-content', ['model'=>$model])?>
</div>
