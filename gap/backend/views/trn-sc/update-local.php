<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnSc */

$this->title = 'Update Local Sales Contract: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sales Contract', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-sc-update">
    <?= $this->render('_form-local', [
        'model' => $model,
    ]) ?>
</div>
