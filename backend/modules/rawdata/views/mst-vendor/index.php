<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstVendorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Vendors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-vendor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Vendor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'telp',
            'fax',
            'email:email',
            //'address:ntext',
            //'cp_name',
            //'aktif:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
