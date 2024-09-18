<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesMaklon */

$this->title = 'Update Trn Kartu Proses Maklon: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Maklons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-maklon-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
