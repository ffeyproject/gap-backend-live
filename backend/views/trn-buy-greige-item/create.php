<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBuyGreigeItem */

$this->title = 'Create Trn Buy Greige Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Buy Greige Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-greige-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
