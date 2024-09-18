<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreigeGroup */

$this->title = 'Create Greige Group';
$this->params['breadcrumbs'][] = ['label' => 'Greige Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-group-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
