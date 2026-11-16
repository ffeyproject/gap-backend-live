<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstK3l */

$this->title = 'Ubah Master K3L: ' . $model->k3l_code;
$this->params['breadcrumbs'][] = ['label' => 'Master K3L', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->k3l_code, 'url' => ['view', 'id' => $model->k3l_code]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-k3l-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
