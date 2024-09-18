<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\KartuProcessDyeingProcess */

$this->title = 'Create Kartu Process Dyeing Process';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Dyeing Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-dyeing-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
