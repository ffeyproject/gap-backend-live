<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $mo TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */
?>

<div class="box collapsed-box" data-widget="box-widget">
    <div class="box-header with-border">
        <h3 class="box-title">MO Information</h3>
        <div class="box-tools">
            <!-- This will cause the box to collapse when clicked -->
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php
        switch ($scGreige->process){
            case $scGreige::PROCESS_DYEING://DYEING
                $viewChild = 'dyeing';
                break;
            case $scGreige::PROCESS_PRINTING://PRINTING
                $viewChild = 'printing';
                break;
            default:
                $viewChild = 'no_content';
                break;
        }

        echo $this->render('/trn-mo/print/'.$viewChild, ['model' => $mo, 'sc'=>$sc, 'scGreige'=>$scGreige]);
        ?>
    </div>
</div>