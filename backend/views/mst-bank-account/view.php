<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstBankAccount */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="box">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'bank_name',
                'acct_no',
                'acct_name',
                'swift_code',
                'address:ntext',
                'correspondence:ntext',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',
            ],
        ]) ?>
    </div>
</div>
