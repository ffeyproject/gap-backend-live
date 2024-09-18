<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\KartuProcessPfpProcess */

$this->title = 'Update Kartu Process Pfp Process: ' . $model->kartu_process_id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Pfp Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kartu_process_id, 'url' => ['view', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kartu-process-pfp-process-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
