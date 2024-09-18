<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnNotif */

$this->title = 'Create Trn Notif';
$this->params['breadcrumbs'][] = ['label' => 'Trn Notifs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-notif-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
