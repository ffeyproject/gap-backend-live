<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */

$this->title = 'Create Greige';
$this->params['breadcrumbs'][] = ['label' => 'Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
