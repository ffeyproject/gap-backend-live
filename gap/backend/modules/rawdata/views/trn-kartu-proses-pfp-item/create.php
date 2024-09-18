<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesPfpItem */

$this->title = 'Create Trn Kartu Proses Pfp Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Pfp Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
