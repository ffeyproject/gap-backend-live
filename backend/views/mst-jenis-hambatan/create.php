<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstJenisHambatan */

$this->title = 'Tambah Master Jenis Hambatan';
$this->params['breadcrumbs'][] = ['label' => 'Master Jenis Hambatan Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-jenis-hambatan-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
