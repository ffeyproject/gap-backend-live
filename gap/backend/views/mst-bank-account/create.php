<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstBankAccount */

$this->title = 'Create Bank Account';
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mst-bank-account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
