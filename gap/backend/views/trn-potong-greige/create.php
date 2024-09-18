<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongGreige */
/* @var $modelsItem common\models\ar\TrnPotongGreigeItem[] */

$this->title = 'Buat Potong Greige';
$this->params['breadcrumbs'][] = ['label' => 'Potong Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-potong-greige-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
