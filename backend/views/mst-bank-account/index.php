<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstBankAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bank Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-bank-account-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'bank_name',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a($data['bank_name'], ['view', 'id' => $data['id']]);
                }
            ],
            'acct_no',
            'acct_name',
            'swift_code',
            //'address:ntext',
            //'correspondence:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            //['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
