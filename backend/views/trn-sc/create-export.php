<?php
use backend\models\form\TrnScLocalForm;

/* @var $this yii\web\View */
/* @var $model TrnScLocalForm */

$this->title = 'Create Export Sales Contract';
$this->params['breadcrumbs'][] = ['label' => 'Sales Contract', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-create">
    <?= $this->render('_form-export', [
        'model' => $model,
    ]) ?>
</div>
