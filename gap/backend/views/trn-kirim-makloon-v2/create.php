<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2 */
/* @var $modelsItem common\models\ar\TrnKirimMakloonV2Item[] */

$this->title = 'Buat Pengiriman Ke Makloon';
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Makloon', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-makloon-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
