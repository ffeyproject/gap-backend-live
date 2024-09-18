<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcessItem */

$this->title = 'Update Trn Terima Makloon Process Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Terima Makloon Process Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-terima-makloon-process-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
