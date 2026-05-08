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

$approvedDataProvider = new ArrayDataProvider([
    'allModels' => !empty($model->approved_history) ? Json::decode($model->approved_history) : [],
    'pagination' => false,
]);
?>

<div class="row">
    <div class="col-md-6">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'responsiveWrap' => false,
            'resizableColumns' => false,
            'toolbar' => false,
            'panel' => [
                'heading' => '<strong>Riwayat Penolakan (Reject Dari Inspecting)</strong>',
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
                        $user = \common\models\User::findOne($data['by']);
                        return $user ? $user->full_name : '-';
                    }
                ]
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= GridView::widget([
            'dataProvider' => $approvedDataProvider,
            'responsiveWrap' => false,
            'resizableColumns' => false,
            'toolbar' => false,
            'panel' => [
                'heading' => '<strong>Riwayat Persetujuan (Masuk Verpacking)</strong>',
                'type' => GridView::TYPE_DEFAULT,
                'before' => false,
                'after' => false,
                'footer' => false
            ],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                'time:datetime',
                [
                    'label'=>'by',
                    'value'=>function($data){
                        $user = \common\models\User::findOne($data['by']);
                        return $user ? $user->full_name : '-';
                    }
                ]
            ],
        ]) ?>
    </div>
</div>


