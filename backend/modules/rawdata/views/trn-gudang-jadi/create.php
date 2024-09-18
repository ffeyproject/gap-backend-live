<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */

$this->title = 'Create Trn Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Trn Gudang Jadis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-gudang-jadi-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
