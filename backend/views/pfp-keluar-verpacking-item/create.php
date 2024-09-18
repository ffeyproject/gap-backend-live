<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpackingItem */

$this->title = 'Create Pfp Keluar Verpacking Item';
$this->params['breadcrumbs'][] = ['label' => 'Pfp Keluar Verpacking Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pfp-keluar-verpacking-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
