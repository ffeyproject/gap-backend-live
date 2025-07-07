<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWoMemo */

$this->title = 'Work Order Memo View - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Wo Memos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-wo-memo-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <!-- <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p> -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'wo_id',
                'value' => function ($model) {
                    return $model->wo ? Html::a($model->wo->no, ['/trn-wo/view', 'id' => $model->wo->id], ['title' => 'Lihat detail WO']) : null;
                },
                'label' => 'No WO',
                'format' => 'raw',
            ],
        ],
    ]) ?>

    <?= $this->render('_memo-perubahan', [
    'model' => $model->wo,
    'users' => $users,
    'userWa' => $userWa,
    ]); ?>

    <iframe src="<?=Url::to(['/trn-wo-memo/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>


</div>