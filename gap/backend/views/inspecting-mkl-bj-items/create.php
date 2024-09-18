<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBjItems */

$this->title = 'Create Inspecting Mkl Bj Items';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Mkl Bj Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-mkl-bj-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
