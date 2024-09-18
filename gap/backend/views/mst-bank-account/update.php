<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstBankAccount */

$this->title = 'Update Bank Account: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="mst-bank-account-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
