<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstGreigeGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Greige Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Greige Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'jenis_kain',
            'nama_kain',
            'qty_per_batch',
            'unit',
            //'nilai_penyusutan',
            //'gramasi_kain',
            //'sulam_pinggir',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'aktif:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
