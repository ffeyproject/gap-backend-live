<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                'id',
                'username',
                'full_name',
                'email:email',
                //'auth_key',
                //'password_hash',
                //'password_reset_token',
                [
                    'label' => 'Roles',
                    'value' => function($data){
                        /* @var $data \backend\modules\user\models\User*/
                        $roles = array_keys($data->getRbacItems()['assigned']);
                        return implode(', ', $roles);
                    }
                ],
                [
                    'attribute'=>'status',
                    'value'=>function($data){
                        /* @var $data \backend\modules\user\models\User*/
                        return \backend\modules\user\models\User::getStatusOptions()[$data->status];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => \backend\modules\user\models\User::getStatusOptions(),
                        'options' => ['placeholder' => 'Select ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]
                ],
                'created_at:datetime',
                // 'updated_at',
                // 'verification_token',

                ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            ],
        ]); ?>
    </div>
</div>
