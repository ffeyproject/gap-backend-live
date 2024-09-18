<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPrinting */

$this->title = 'Create Kartu Proses Printing';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-printing-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
