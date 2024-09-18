<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstPapperTube */

$this->title = 'Ubah Papper Tube: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Papper Tube', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>

<div class="mst-papper-tube-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
