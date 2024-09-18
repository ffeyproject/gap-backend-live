<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnWoMemo */

$this->title = 'Create Trn Wo Memo';
$this->params['breadcrumbs'][] = ['label' => 'Trn Wo Memos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-memo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
