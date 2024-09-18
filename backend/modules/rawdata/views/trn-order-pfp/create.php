<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnOrderPfp */

$this->title = 'Create Trn Order Pfp';
$this->params['breadcrumbs'][] = ['label' => 'Trn Order Pfps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-order-pfp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
