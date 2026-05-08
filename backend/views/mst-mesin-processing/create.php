<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */

$this->title = 'Create Mesin Processing';
$this->params['breadcrumbs'][] = ['label' => 'Mesin Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mst-mesin-processing-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
