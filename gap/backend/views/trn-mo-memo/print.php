<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnMoMemo;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnMoMemo */
/* @var $mo TrnMo */
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>MEMO PERUBAHAN</h4>
        <strong>NOMOR MO: <?=$mo->no?></strong>
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
                    'value'=>$mo->creatorName
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