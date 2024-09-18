<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstPapperTube */

$this->title = 'Tambah Papper Tube';
$this->params['breadcrumbs'][] = ['label' => 'Papper Tube', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mst-papper-tube-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
