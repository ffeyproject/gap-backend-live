<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinish */

$this->title = 'Create Jual Ex Finish';
$this->params['breadcrumbs'][] = ['label' => 'Jual Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jual-ex-finish-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
