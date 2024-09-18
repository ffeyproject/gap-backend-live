<?php

use common\components\ajax_modal\AjaxModal;
use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $mo TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Persetujuan Working Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="trn-wo-view">

        <p>
            <?=Html::a('Setujui', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'approveWo(event);',
                'title' => 'Approve WO: '.$model->id
            ]);?>
            <?=Html::a('Tolak', ['reject', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'onclick' => 'rejectWo(event);',
                'title' => 'Reject WO: '.$model->id
            ]);?>
        </p>

        <iframe src="<?=\yii\helpers\Url::to(['/trn-wo/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>

        <?=$this->render('/trn-wo/_persetujuan', ['model'=>$model])?>
    </div>

<?php
$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(__DIR__.'/js/view.js');
$this->registerJs($js, $this::POS_END);