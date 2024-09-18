<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinishItem */

$this->title = 'Update Trn Terima Makloon Finish Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Terima Makloon Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-terima-makloon-finish-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
