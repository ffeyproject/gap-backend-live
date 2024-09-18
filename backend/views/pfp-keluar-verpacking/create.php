<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpacking */
/* @var $modelsItem common\models\ar\PfpKeluarVerpackingItem[] */

$this->title = 'Create Pfp Keluar Verpacking';
$this->params['breadcrumbs'][] = ['label' => 'Pfp Keluar Verpackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pfp-keluar-verpacking-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
