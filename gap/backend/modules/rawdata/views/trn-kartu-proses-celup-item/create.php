<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesCelupItem */

$this->title = 'Create Trn Kartu Proses Celup Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Celup Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-celup-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
