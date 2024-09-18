<?php

use common\models\ar\TrnTerimaMakloonFinishItem;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinish */
/* @var  $modelsItem TrnTerimaMakloonFinishItem[]*/

$this->title = 'Ubah Penerimaan Makloon Finish: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Makloon Finish', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-terima-makloon-finish-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
