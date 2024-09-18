<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongGreigeItem */

$this->title = 'Create Trn Potong Greige Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Potong Greige Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-potong-greige-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
