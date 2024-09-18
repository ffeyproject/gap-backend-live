<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockPfp */

$this->title = 'Create Trn Stock Pfp';
$this->params['breadcrumbs'][] = ['label' => 'Trn Stock Pfps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-pfp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
