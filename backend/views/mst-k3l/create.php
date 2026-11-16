<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstK3l */

$this->title = 'Tambah Master K3L';
$this->params['breadcrumbs'][] = ['label' => 'Master K3L', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-k3l-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
