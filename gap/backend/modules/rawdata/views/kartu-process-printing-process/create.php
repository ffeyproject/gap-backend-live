<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\KartuProcessPrintingProcess */

$this->title = 'Create Kartu Process Printing Process';
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Printing Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-printing-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
