<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\SysSetting */

$this->title = 'Create Sys Setting';
$this->params['breadcrumbs'][] = ['label' => 'Sys Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
