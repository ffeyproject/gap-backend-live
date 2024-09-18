<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMemoPerubahanData */

$this->title = 'Create Trn Memo Perubahan Data';
$this->params['breadcrumbs'][] = ['label' => 'Trn Memo Perubahan Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-memo-perubahan-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
