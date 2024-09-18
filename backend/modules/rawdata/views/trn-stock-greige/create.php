<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnStockGreige */

$this->title = 'Create Trn Stock Greige';
$this->params['breadcrumbs'][] = ['label' => 'Trn Stock Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-greige-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
