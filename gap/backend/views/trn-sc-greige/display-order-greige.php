<?php
use common\models\ar\TrnScGreige;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnScGreige */
?>

<iframe src="<?=Url::to(['/trn-sc-greige/print-order-greige', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
