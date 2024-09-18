<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRepair */

$this->title = 'Ubah Memo Repair: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Memo Repair', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-memo-repair-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
