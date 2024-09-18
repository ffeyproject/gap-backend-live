<?php

use common\models\ar\TrnTerimaMakloonProcessItem;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcess */
/* @var  $modelsItem TrnTerimaMakloonProcessItem[]*/

$this->title = 'Ubah Penerimaan Makloon Proses: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Makloon Proses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-terima-makloon-process-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
