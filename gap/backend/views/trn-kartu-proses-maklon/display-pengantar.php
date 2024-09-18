<?php
use common\models\ar\TrnScAgen;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnScAgen */

?>

<iframe src="<?=Url::to(['print-pengantar', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
