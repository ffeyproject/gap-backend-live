<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoPerubahanData */

$this->title = 'Buat Memo Perubahan Data';
$this->params['breadcrumbs'][] = ['label' => 'Memo Perubahan Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-memo-perubahan-data-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
