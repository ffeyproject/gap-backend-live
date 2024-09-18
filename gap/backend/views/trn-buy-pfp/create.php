<?php
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnBuyPfpItem;

/* @var $this yii\web\View */
/* @var $model TrnBuyPfp */
/* @var $modelsItem TrnBuyPfpItem*/

$this->title = 'Tambah Masuk PFP';
$this->params['breadcrumbs'][] = ['label' => 'Masuk PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="trn-buy-pfp-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
