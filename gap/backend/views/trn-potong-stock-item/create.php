<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStockItem */

$this->title = 'Create Trn Potong Stock Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Potong Stock Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-potong-stock-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
