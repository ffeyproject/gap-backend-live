<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelup */

$this->title = 'Ubah Kartu Proses Celup: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-kartu-proses-celup-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
