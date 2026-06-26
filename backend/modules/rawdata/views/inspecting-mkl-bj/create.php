<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */

$this->title = 'Create Inspecting Mkl Bj';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Mkl Bjs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-mkl-bj-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
