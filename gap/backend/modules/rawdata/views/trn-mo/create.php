<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMo */

$this->title = 'Create Trn Mo';
$this->params['breadcrumbs'][] = ['label' => 'Trn Mos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
