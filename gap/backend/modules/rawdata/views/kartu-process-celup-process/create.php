<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\KartuProcessCelupProcess */

$this->title = 'Create Kartu Process Celup Process';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Celup Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-celup-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
