<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstLocation */

$this->title = 'Create Location';
$this->params['breadcrumbs'][] = ['label' => 'Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-location-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
