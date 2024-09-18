<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinishItem */

$this->title = 'Update Jual Ex Finish Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jual Ex Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="jual-ex-finish-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
