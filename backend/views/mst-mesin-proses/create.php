<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProses */

$this->title = 'Tambah Master Mesin Proses';
$this->params['breadcrumbs'][] = ['label' => 'Master Mesin Proses Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-mesin-proses-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
