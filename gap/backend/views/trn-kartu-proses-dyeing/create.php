<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$this->title = 'Create Kartu Proses Dyeing';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-kartu-proses-dyeing-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
