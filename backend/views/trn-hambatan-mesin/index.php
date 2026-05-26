<?php
use common\models\ar\TrnHambatanMesin;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnHambatanMesinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hambatan Per Mesin';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-hambatan-mesin-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']) .
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'toolbar' => [
            '{export}',
        ],
        'columns' => [
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view} {update} {delete}'],

            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'mst_mesin_proses_id',
                'label' => 'Nama/Nomor Mesin',
                'value' => function ($data) {
                    /* @var $data TrnHambatanMesin */
                    return $data->mstMesinProses->nama_mesin ?? '-';
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\ar\MstMesinProses::find()->asArray()->all(), 'id', 'nama_mesin'),
            ],
            [
                'attribute' => 'model_mesin',
                'label' => 'Model Mesin',
                'value' => function ($data) {
                    /* @var $data TrnHambatanMesin */
                    return $data->mstMesinProses->model_mesin ?? '-';
                },
            ],
            [
                'attribute' => 'tanggal',
                'label' => 'Tanggal',
                'format' => 'date',
            ],
            [
                'label' => 'Jumlah Hambatan',
                'value' => function ($data) {
                    /* @var $data TrnHambatanMesin */
                    return count($data->trnHambatanMesinItems);
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Dibuat Pada',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
            ],
        ],
    ]); ?>
</div>
