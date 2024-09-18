<?php
use common\models\ar\{ TrnInspecting, InspectingMklBj };
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Fixing Data - Update';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-fixing-data-update">
    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => NULL,
                'id' => 'fixingData',
                'resizableColumns' => false,
                'responsiveWrap' => false,
                'pjax' => true,
                'toolbar' => [
                    '{toggleData}',
                    '{export}' => ['visible' => false]
                ],
                'panel' => [
                    'type' => 'default',
                    'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
                    'after'=>false,
                ],
                'showPageSummary'=>false,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    'id',
                    'no',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{customButton}',
                        'buttons' => [
                            'customButton' => function ($url, $model, $key) {
                                $queryParams = Yii::$app->request->getQueryParams();
                                if ($model['have_checked'] == 1) {
                                    return '<span class="glyphicon glyphicon-ban-circle disabled"></span>';
                                } else {
                                    return Html::a('<span class="glyphicon glyphicon-ok"></span>', ['update', 'id' => $model['id']] + $queryParams, [
                                        'title' => Yii::t('yii', 'Fix'),
                                        'data-pjax' => '0',
                                        'data-method' => 'POST', // Set the HTTP method to POST
                                        'data-confirm' => 'Apakah kamu yakin untuk fixing data id '.$model['id'].' ini ?',
                                    ]);
                                }
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>