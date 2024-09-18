<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesPfp */

$this->title = 'Create Trn Kartu Proses Pfp';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Pfps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
