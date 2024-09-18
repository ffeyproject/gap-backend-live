<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesDyeingItem */

$this->title = 'Create Trn Kartu Proses Dyeing Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Dyeing Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-dyeing-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
