<?php

use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\MstProcessDyeing;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\i18n\Formatter;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessDyeing[]*/
/* @var $processesUlang array*/
/* @var $formatter Formatter */

unset($attrsLabels['use_jetblack']);

$this->registerCss('
    .ctn-disable{background-color:black;}
    .table-sticky-container {
        max-height: 500px;
        overflow-y: auto;
        position: relative;
    }
    .table-sticky-container th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f9f9f9 !important;
        box-shadow: inset 0 -1px 0 #f4f4f4, inset 0 1px 0 #f4f4f4;
    }
');
$disabledCol = '<button class="btn btn-xs btn-danger btn-flat btn-block" disabled="disabled">-</button>';
$btnUnset = Html::button('&nbsp;', ['class'=>'btn btn-xs btn-warning btn-flat btn-block', 'disabled'=>'disabled']);
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><strong>Proses</strong></h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body">
        <?php if (!empty($attrsLabels)):?>
            <div class="table-sticky-container">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <?php
                        foreach ($attrsLabels as $key=>$attrsLabel){
                            if($key !== 'id'){
                                echo '<th>'.$attrsLabel.'</th>';
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $jetblackHasValue = false;
                    foreach ($processModels as $item) {
                        if ($item->use_jetblack) {
                            $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id' => $model->id, 'process_id' => $item->id]);
                            if ($pcModel !== null) {
                                $jetblackHasValue = true;
                                break;
                            }
                        }
                    }

                    $firstJetblack = true;
                    foreach ($processModels as $item){
                        if ($item->use_jetblack) {
                            if ($firstJetblack) {
                                $colCount = count($attrsLabels); // No 'Ulang' column
                                $chevronClass = $jetblackHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="jetblack-header-row" style="background-color: #3c8dbc; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="jetblack-icon"></i> <strong>PROSES JETBLACK (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstJetblack = false;
                            }
                            $rowStyle = $jetblackHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="jetblack-row" ' . $rowStyle . '>';
                        } else {
                            echo '<tr>';
                        }
                        foreach ($item->attributes as $key=>$value){
                            if(!in_array($key, ['id', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by', 'max_pengulangan', 'use_jetblack'])){
                                if($key !== 'nama_proses'){
                                    if($value){
                                        echo '<td>';

                                        $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$item->id]);
                                        if($pcModel === null){
                                            $lblBtn = Html::button('&nbsp;', ['class'=>'btn btn-xs btn-warning btn-flat btn-block', 'disabled'=>'disabled']);
                                        }else{
                                            $datas = Json::decode($pcModel->value);
                                            if(isset($datas[$key]) && !empty($datas[$key])){
                                                $lblBtn = $datas[$key];
                                            }else{
                                                $lblBtn = Html::button('&nbsp;', ['class'=>'btn btn-xs btn-warning btn-flat btn-block', 'disabled'=>'disabled']);
                                            }
                                        }

                                        $label = $item->getAttributeLabel($key);

                                        echo $lblBtn;

                                        echo '</td>';
                                    }else{
                                        echo '<td><button class="btn btn-xs btn-danger btn-flat btn-block" disabled="disabled">-</button></td>';
                                    }
                                }else{
                                    echo '<td>'.$value.'</td>';
                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php else:?>
            <p class="text-danger">Tidak ada data pada master proses dyeing, proses tidak bisa dijalankan</p>
        <?php endif;?>
    </div>

    <div class="box-footer"></div>
</div>

<?=$this->render('proses_ulang', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);?>

<?php
$this->registerJs("
    $(document).on('click', '.jetblack-header-row', function() {
        $('.jetblack-row').toggle();
        var icon = $('#jetblack-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });
");
?>