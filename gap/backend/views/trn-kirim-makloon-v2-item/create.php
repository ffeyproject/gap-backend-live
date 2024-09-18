<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2Item */

$this->title = 'Create Trn Kirim Makloon V2 Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kirim Makloon V2 Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-makloon-v2-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
