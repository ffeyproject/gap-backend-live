<?php
use common\models\ar\InspectingMklBjItems;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */
/* @var $modelItem InspectingMklBjItems */

$this->title = 'Create Inspecting Makloon Dan Barang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Makloon Dan Barang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-mkl-bj-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelItem' => $modelItem,
        'items' => []
    ]) ?>
</div>
