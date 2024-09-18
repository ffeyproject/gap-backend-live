<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRepair */

$this->title = 'Buat Memo Repair';
$this->params['breadcrumbs'][] = ['label' => 'emo Repair', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-memo-repair-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
