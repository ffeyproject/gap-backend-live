<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnSc */

$this->title = 'Create Trn Sc';
$this->params['breadcrumbs'][] = ['label' => 'Trn Scs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
