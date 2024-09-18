<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesMaklon */
/* @var $modelsItem common\models\ar\TrnKartuProsesMaklonItem[] */

$this->title = 'Create Kartu Proses Maklon';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Maklon', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-maklon-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem,
    ]) ?>
</div>
