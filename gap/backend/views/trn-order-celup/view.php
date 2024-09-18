<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderCelup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-order-celup-view">

    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <div class="box">
        <div class="box-header with-border">
            <!--<h3 class="box-title"><strong></strong></h3>
            <div class="box-tools pull-right"></div>-->
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label'=>'Nomor SC',
                        'value'=>$model->sc->no
                    ],
                    [
                        'label'=>'Greige Group',
                        'value'=>$model->greigeGroup->nama_kain
                    ],
                    [
                        'label'=>'Handling',
                        'value'=>$model->handling->name
                    ],
                    [
                        'label'=>'Greige',
                        'value'=>$model->greige->nama_kain
                    ],
                    'no_urut',
                    'no',
                    'qty:decimal',
                    'color',
                    'note:ntext',
                    [
                        'label'=>'Status',
                        'value'=>$model::statusOptions()[$model->status]
                    ],
                    'date:date',
                    'created_at:datetime',
                    'created_by',
                    'updated_at:datetime',
                    'updated_by',
                ],
            ]) ?>
        </div>
    </div>

</div>
