<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Create Gudang Jadi Mutasi';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="gudang-jadi-mutasi-create">
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
