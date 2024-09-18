<?php

use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;

/* @var $this yii\web\View */
/* @var $modelHeader InspectingHeaderForm */
/* @var $modelItem InspectingItemsForm */

$this->title = 'Create Inspecting';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="trn-inspecting-create">
    <?= $this->render('_form', [
        'modelHeader' => $modelHeader,
        'modelItem' => $modelItem
    ]) ?>
</div>
