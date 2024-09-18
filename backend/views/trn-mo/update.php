<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Update Trn Mo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Mos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-mo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
