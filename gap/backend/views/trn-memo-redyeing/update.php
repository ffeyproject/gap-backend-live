<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRedyeing */

$this->title = 'Ubah Memo Redyeing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Memo Redyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-memo-redyeing-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
