<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnBuyPfpItem */

$this->title = 'Create Trn Buy Pfp Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Buy Pfp Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-pfp-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
