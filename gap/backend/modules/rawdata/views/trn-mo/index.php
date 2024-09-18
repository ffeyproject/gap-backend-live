<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnMoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Mos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Mo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sc_id',
            'sc_greige_id',
            'process',
            'approval_id',
            //'approved_at',
            //'no_urut',
            //'no',
            //'date',
            //'re_wo',
            //'design',
            //'article',
            //'strike_off:ntext',
            //'heat_cut:boolean',
            //'sulam_pinggir',
            //'border_size',
            //'block_size',
            //'foil:boolean',
            //'face_stamping:ntext',
            //'selvedge_stamping',
            //'selvedge_continues',
            //'side_band',
            //'tag',
            //'hanger',
            //'label',
            //'folder',
            //'album',
            //'joint:boolean',
            //'joint_qty',
            //'packing_method',
            //'shipping_method',
            //'shipping_sorting',
            //'plastic',
            //'arsip',
            //'jet_black:boolean',
            //'piece_length',
            //'est_produksi',
            //'est_packing',
            //'target_shipment',
            //'jenis_gudang',
            //'posted_at',
            //'closed_at',
            //'closed_by',
            //'closed_note:ntext',
            //'reject_notes:ntext',
            //'batal_at',
            //'batal_by',
            //'batal_note:ntext',
            //'status',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
