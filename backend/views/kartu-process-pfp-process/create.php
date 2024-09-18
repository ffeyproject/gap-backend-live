<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\KartuProcessPfpProcess */

$this->title = 'Create Kartu Process Pfp Process';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Pfp Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-pfp-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
