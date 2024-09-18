<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnBuyPfp */

$this->title = 'Create Trn Buy Pfp';
$this->params['breadcrumbs'][] = ['label' => 'Trn Buy Pfps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-pfp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
