<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnHambatanMesin */
/* @var $items common\models\ar\TrnHambatanMesinItem[] */

$this->title = 'Tambah Hambatan Per Mesin';
$this->params['breadcrumbs'][] = ['label' => 'Hambatan Per Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-hambatan-mesin-create">
    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
    ]) ?>
</div>
