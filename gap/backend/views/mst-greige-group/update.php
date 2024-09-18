<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreigeGroup */

$this->title = 'Update Greige Group: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greige Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-greige-group-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
