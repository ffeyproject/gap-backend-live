<?php
use common\models\ar\InspectingMklBjItems;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */
/* @var $modelItem InspectingMklBjItems */
/* @var $items array */

$this->title = 'Upgrade Inspecting Makloon Dan Barang Jadi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Makloon Dan Barang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Upgrade';
?>
<div class="inspecting-mkl-bj-upgrade">
    <?= $this->render('_form_upgrade', [
        'model' => $model,
        'modelItem' => $modelItem,
        'items' => $items
    ]) ?>
</div>
