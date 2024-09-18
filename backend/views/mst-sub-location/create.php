<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstSubLocation */

$this->title = 'Create Sub Location';
$this->params['breadcrumbs'][] = ['label' => 'Sub Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-sub-location-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
