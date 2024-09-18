<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelup */

$this->title = 'Buat Kartu Proses Celup';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-celup-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
