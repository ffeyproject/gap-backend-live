<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluar */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tambah Greige Keluar Makloon';
$this->params['breadcrumbs'][] = ['label' => 'Greige Keluar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-greige-keluar-create">
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
