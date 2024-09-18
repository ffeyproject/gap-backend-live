<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScGreige */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Sc Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-sc-greige-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sc_id',
            'greige_group_id',
            'process',
            'lebar_kain',
            'merek',
            'grade',
            'piece_length',
            'unit_price',
            'price_param',
            'qty',
            'woven_selvedge:ntext',
            'note:ntext',
            'closed:boolean',
            'closing_note:ntext',
            'no_order_greige',
            'no_urut_order_greige',
            'order_greige_note:ntext',
        ],
    ]) ?>

</div>
