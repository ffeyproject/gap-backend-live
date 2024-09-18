<?php
use common\models\ar\TrnScAgen;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnScAgen */

?>

<iframe src="<?=Url::to(['/trn-sc-agen/display-loa', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
