<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeingItem */

$this->title = 'Update Trn Kartu Proses Dyeing Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Dyeing Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-dyeing-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
