<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerItem */

$this->title = 'Create Trn Kirim Buyer Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kirim Buyer Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
