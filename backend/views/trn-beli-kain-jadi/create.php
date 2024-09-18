<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBeliKainJadi */
/* @var  $modelsItem \common\models\ar\TrnBeliKainJadiItem[]*/

$this->title = 'Tambah Beli Kain Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Beli Kain Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-beli-kain-jadi-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
