<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBjItems */

$this->title = 'Update Inspecting Mkl Bj Items: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Mkl Bj Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inspecting-mkl-bj-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
