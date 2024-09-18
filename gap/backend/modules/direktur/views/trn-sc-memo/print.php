<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScMemo;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnScMemo */
/* @var $sc TrnSc */
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>MEMO PERUBAHAN</h4>
        <strong>NOMOR SC: <?=$sc->no?></strong>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?=DetailView::widget([
            'model' => $sc,
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-right: 5px;'
            ],
            'attributes' => [

            ]
        ])?>
    </div>

    <div class="col-xs-6">
        <?=DetailView::widget([
            'model' => $sc,
            'options' => [
                'class' => 'table table-bordered small',
                'style' => 'margin-left: 5px;'
            ],
            'attributes' => [

            ]
        ])?>
    </div>
</div>

<strong>Memo:</strong>
<p><?=$model->memo?></p>

<div class="row">
    <div class="col-xs-6">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view small'],
            //'template' => '<tr><th style="width: 25%;">{label}</th><td{contentOptions}>: {value}</td></tr>',
            'attributes' => [
                [
                    'label' => 'Dibuat OLeh',
                    'value' => $sc->creatorName
                ],
                [
                    'label' => 'Pada',
                    'attribute' => 'created_at',
                    'format' => 'datetime'
                ]
            ],
        ]) ?>
    </div>

    <div class="col-xs-6">

    </div>
</div>