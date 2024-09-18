<?php
use common\models\ar\TrnKartuProsesDyeing;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */

$dataProvider = new ArrayDataProvider([
    'allModels' => Json::decode($model->reject_notes),
    'pagination' => false,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'toolbar' => false,
    'panel' => [
        'heading' => '<strong>Riwayat Penolakan</strong>',
        'type' => GridView::TYPE_DEFAULT,
        'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'time:datetime',
        'note',
        [
            'label'=>'by',
            'value'=>function($data){
                return \common\models\User::findOne($data['by'])->full_name;
            }
        ]
    ],
]);

