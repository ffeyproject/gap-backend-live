<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyerItem */

$this->title = 'Create Trn Retur Buyer Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Retur Buyer Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-retur-buyer-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
