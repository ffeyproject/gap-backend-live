<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2 */
/* @var $modelsItem common\models\ar\TrnKirimMakloonV2Item[] */

$this->title = 'Ubah Pengiriman Ke Makloon: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Makloon', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>

<div class="trn-kirim-makloon-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
