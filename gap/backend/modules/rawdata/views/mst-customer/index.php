<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Customer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cust_no',
            'name',
            'telp',
            'fax',
            //'email:email',
            //'address:ntext',
            //'cp_name',
            //'cp_phone',
            //'cp_email:email',
            //'npwp',
            //'aktif:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
