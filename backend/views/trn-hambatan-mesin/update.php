<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnHambatanMesin */
/* @var $items common\models\ar\TrnHambatanMesinItem[] */

$this->title = 'Ubah Hambatan: Shift ' . $model->shift . ' (' . $model->tanggal . ')';
$this->params['breadcrumbs'][] = ['label' => 'Hambatan Per Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Shift ' . $model->shift . ' (' . $model->tanggal . ')', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-hambatan-mesin-update">
    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
    ]) ?>
</div>
