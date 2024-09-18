<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */

$this->title = 'Create Kartu Proses PFP';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
