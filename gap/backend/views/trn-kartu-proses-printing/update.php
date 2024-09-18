<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPrinting */

$this->title = 'Update Kartu Proses Printing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-printing-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
