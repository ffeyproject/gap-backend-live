<?php
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnBuyPfpItem;

/* @var $this yii\web\View */
/* @var $model TrnBuyPfp */
/* @var $modelsItem TrnBuyPfpItem*/

$this->title = 'Update Beli Greige Jadi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Update Beli Greige Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="trn-buy-pfp-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
