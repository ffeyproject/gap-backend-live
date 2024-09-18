<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */

$this->title = 'Update Trn Gudang Jadi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Gudang Jadis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-gudang-jadi-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
