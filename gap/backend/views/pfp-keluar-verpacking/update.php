<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpacking */
/* @var $modelsItem common\models\ar\PfpKeluarVerpackingItem[] */

$this->title = 'Update Pfp Keluar Verpacking: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pfp Keluar Verpackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pfp-keluar-verpacking-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
