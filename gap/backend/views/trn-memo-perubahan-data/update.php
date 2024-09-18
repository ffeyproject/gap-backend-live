<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoPerubahanData */

$this->title = 'Ubah Memo Perubahan Data: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Memo Perubahan Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-memo-perubahan-data-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
