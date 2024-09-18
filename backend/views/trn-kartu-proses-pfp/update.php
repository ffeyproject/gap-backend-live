<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */

$this->title = 'Update Kartu Proses PFP: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-pfp-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
