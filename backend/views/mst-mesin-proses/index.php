<?php
use common\models\ar\MstMesinProses;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstMesinProsesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Mesin Proses Processing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-mesin-proses-index">
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
                'attribute' => 'nama_mesin',
                'label' => 'Nama/Nomor Mesin',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data MstMesinProses */
                    return Html::a(Html::encode($data->nama_mesin), ['view', 'id' => $data->id]);
                },
            ],
            [
                'attribute' => 'model_mesin',
                'label' => 'Model Mesin',
            ],
            [
                'label' => 'Jenis Hambatan',
                'value' => function ($data) {
                    /* @var $data MstMesinProses */
                    $hambatans = \yii\helpers\ArrayHelper::getColumn($data->mstJenisHambatans, 'nama');
                    if (empty($hambatans)) {
                        return '-';
                    }
                    return implode(', ', $hambatans);
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Dibuat Pada',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Diperbarui Pada',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
            ],
        ],
    ]); ?>
</div>
