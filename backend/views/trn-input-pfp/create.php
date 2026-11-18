<?php
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnBuyPfpItem;

/* @var $this yii\web\View */
/* @var $model TrnBuyPfp */
/* @var $modelsItem TrnBuyPfpItem[] */

$this->title = 'Tambah Input PFP (Gudang 2)';
$this->params['breadcrumbs'][] = ['label' => 'Input PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-input-pfp-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem,
    ]) ?>
</div>
