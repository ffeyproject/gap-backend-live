<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstBankAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Bank Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-bank-account-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Bank Account', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'bank_name',
            'acct_no',
            'acct_name',
            'swift_code',
            //'address:ntext',
            //'correspondence:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
