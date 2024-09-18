<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBeliKainJadi */
/* @var  $modelsItem \common\models\ar\TrnBeliKainJadiItem[]*/

$this->title = 'Ubah Beli Kain Jadi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Beli Kain Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-beli-kain-jadi-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
