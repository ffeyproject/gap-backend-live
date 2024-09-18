<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRedyeing */

$this->title = 'Buat Memo Redyeing';
$this->params['breadcrumbs'][] = ['label' => 'Memo Redyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-memo-redyeing-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
