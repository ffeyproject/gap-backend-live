<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstCustomer */

$this->title = 'Create Mst Customer';
$this->params['breadcrumbs'][] = ['label' => 'Mst Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
