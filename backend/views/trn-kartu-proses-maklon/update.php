<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesMaklon */
/* @var $modelsItem common\models\ar\TrnKartuProsesMaklonItem[] */

$this->title = 'Update Kartu Proses Maklon: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Maklons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-maklon-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem,
    ]) ?>
</div>
