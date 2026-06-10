<?php

use backend\assets\JqueryUiAsset;
use backend\components\ajax_modal\AjaxModal;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\MstProcessDyeing;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
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
                        <th>Ulang</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $existingPcs = KartuProcessDyeingProcess::find()
                        ->where(['kartu_process_id' => $model->id])
                        ->select('process_id')
                        ->column();

                    $jetblackHasValue = false;
                    $topingHasValue = false;
                    $topingLevelHasValue = false;
                    $levelingHasValue = false;
                    $rcHasValue = false;
                    $rfUlangHasValue = false;
                    
                    foreach ($processModels as $item) {
                        if (in_array($item->id, $existingPcs)) {
                            if ($item->use_jetblack) $jetblackHasValue = true;
                            if (in_array($item->nama_proses, ['Toping 1', 'Toping 2', 'Toping 3', 'Toping 4', 'Toping 5'])) $topingHasValue = true;
                            if (in_array($item->nama_proses, ['Toping Level 1', 'Toping Level 2', 'Toping Level 3', 'Toping Level 4', 'Toping Level 5'])) $topingLevelHasValue = true;
                            if (in_array($item->nama_proses, ['Leveling 1', 'Leveling 2', 'Leveling 3', 'Leveling 4', 'Leveling 5'])) $levelingHasValue = true;
                            if (in_array($item->nama_proses, ['RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5'])) $rcHasValue = true;
                            if (in_array($item->nama_proses, ['RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4'])) $rfUlangHasValue = true;
                        }
                    }

                    $firstJetblack = true;
                    $firstToping = true;
                    $firstTopingLevel = true;
                    $firstLeveling = true;
                    $firstRc = true;
                    $firstRfUlang = true;
                    
                    foreach ($processModels as $item){
                        $isToping = in_array($item->nama_proses, ['Toping 1', 'Toping 2', 'Toping 3', 'Toping 4', 'Toping 5']);
                        $isTopingLevel = in_array($item->nama_proses, ['Toping Level 1', 'Toping Level 2', 'Toping Level 3', 'Toping Level 4', 'Toping Level 5']);
                        $isLeveling = in_array($item->nama_proses, ['Leveling 1', 'Leveling 2', 'Leveling 3', 'Leveling 4', 'Leveling 5']);
                        $isRc = in_array($item->nama_proses, ['RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5']);
                        $isRfUlang = in_array($item->nama_proses, ['RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4']);

                        if ($item->use_jetblack) {
                            if ($firstJetblack) {
                                $colCount = count($attrsLabels) + 1; // including 'Ulang' column
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
                        } elseif ($isToping) {
                            if ($firstToping) {
                                $colCount = count($attrsLabels) + 1;
                                $chevronClass = $topingHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="toping-header-row" style="background-color: #e08e0b; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="toping-icon"></i> <strong>PROSES TOPING (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstToping = false;
                            }
                            $rowStyle = $topingHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="toping-row" ' . $rowStyle . '>';
                        } elseif ($isTopingLevel) {
                            if ($firstTopingLevel) {
                                $colCount = count($attrsLabels) + 1;
                                $chevronClass = $topingLevelHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="toping-level-header-row" style="background-color: #00c0ef; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="toping-level-icon"></i> <strong>PROSES TOPING LEVEL (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstTopingLevel = false;
                            }
                            $rowStyle = $topingLevelHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="toping-level-row" ' . $rowStyle . '>';
                        } elseif ($isLeveling) {
                            if ($firstLeveling) {
                                $colCount = count($attrsLabels) + 1;
                                $chevronClass = $levelingHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="leveling-header-row" style="background-color: #f39c12; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="leveling-icon"></i> <strong>PROSES LEVELING (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstLeveling = false;
                            }
                            $rowStyle = $levelingHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="leveling-row" ' . $rowStyle . '>';
                        } elseif ($isRc) {
                            if ($firstRc) {
                                $colCount = count($attrsLabels) + 1;
                                $chevronClass = $rcHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="rc-header-row" style="background-color: #00a65a; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="rc-icon"></i> <strong>PROSES RC (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstRc = false;
                            }
                            $rowStyle = $rcHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="rc-row" ' . $rowStyle . '>';
                        } elseif ($isRfUlang) {
                            if ($firstRfUlang) {
                                $colCount = count($attrsLabels) + 1;
                                $chevronClass = $rfUlangHasValue ? 'glyphicon-chevron-down' : 'glyphicon-chevron-right';
                                echo '<tr class="rfulang-header-row" style="background-color: #dd4b39; color: white; cursor: pointer; font-weight: bold;">';
                                echo '<td colspan="' . $colCount . '" class="text-center">';
                                echo '<i class="glyphicon ' . $chevronClass . '" id="rfulang-icon"></i> <strong>PROSES RF ULANG (Klik untuk Expand / Collapse)</strong>';
                                echo '</td>';
                                echo '</tr>';
                                $firstRfUlang = false;
                            }
                            $rowStyle = $rfUlangHasValue ? '' : 'style="display: none;"';
                            echo '<tr class="rfulang-row" ' . $rowStyle . '>';
                        } else {
                            echo '<tr>';
                        }
                        foreach ($attrsLabels as $key=>$label){
                            if($key !== 'id'){
                                $value = $item->getAttribute($key);
                                if ($key === 'nama_proses') {
                                    echo '<td>'.$value.'</td>';
                                } else {
                                    $isKeterangan = ($key === 'keterangan');
                                    if($value || $isKeterangan){
                                        echo '<td>';

                                        $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$item->id]);
                                        if($pcModel === null){
                                            $lblBtn = '<span class="label label-success">Set</span>';
                                        }else{
                                            $datas = Json::decode($pcModel->value);
                                            if(isset($datas[$key]) && !empty($datas[$key])){
                                                $lblBtn = $datas[$key].' <span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>';
                                            }else{
                                                $lblBtn = '<span class="label label-success">Set</span>';
                                            }
                                        }

                                        switch ($key){
                                            case 'tanggal':
                                                $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$item->id, 'attr'=>$key], [
                                                    'onclick' => 'setDateInput(event, "'.$label.' '.$item->nama_proses.'");',
                                                    'title' => 'Set '.$label.' '.$item->nama_proses
                                                ]);
                                                break;
                                            case 'start':
                                            case 'stop':
                                                $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$item->id, 'attr'=>$key], [
                                                    'onclick' => 'setTimeInput(event, "Waktu '.$label.' '.$item->nama_proses.'");',
                                                    'title' => 'Set '.$label.' '.$item->nama_proses
                                                ]);
                                                break;
                                            case 'shift_group':
                                                $curVal = '';
                                                if ($pcModel !== null) {
                                                    $datas = Json::decode($pcModel->value);
                                                    $curVal = isset($datas[$key]) ? $datas[$key] : '';
                                                }
                                                $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$item->id, 'attr'=>$key], [
                                                    'onclick' => 'setShiftGroupInput(event, "'.$label.' '.$item->nama_proses.'", "'.$curVal.'");',
                                                    'title' => 'Set '.$label.' '.$item->nama_proses
                                                ]);
                                                break;
                                            case 'no_mesin':
                                                $curVal = '';
                                                if ($pcModel !== null) {
                                                    $datas = Json::decode($pcModel->value);
                                                    $curVal = isset($datas[$key]) ? $datas[$key] : '';
                                                }
                                                $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$item->id, 'attr'=>$key], [
                                                    'onclick' => 'setNoMesinInput(event, "'.$label.' '.$item->nama_proses.'", '.$item->id.', "'.$curVal.'");',
                                                    'title' => 'Set '.$label.' '.$item->nama_proses
                                                ]);
                                                break;
                                            default:
                                                $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$item->id, 'attr'=>$key], [
                                                    'onclick' => 'setTextInput(event, "'.$label.' '.$item->nama_proses.'");',
                                                    'title' => 'Set '.$label.' '.$item->nama_proses
                                                ]);
                                        }

                                        echo $btn;

                                        echo '</td>';
                                    }else{
                                        echo '<td><button class="btn btn-xs btn-danger btn-flat btn-block" disabled="disabled">-</button></td>';
                                    }
                                }
                            }
                        }

                        echo '<td>';
                        echo Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', ['re-proses', 'id'=>$model->id, 'proses_id'=>$item->id], [
                            'class'=>'btn btn-xs btn-danger btn-flat btn-block',
                            'onclick' => 'setProsesUlang(event, "Masukan keterangan Re Proses");',
                            'title' => 'Proses Ulang '.$item->nama_proses
                        ]);
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php else:?>
        <p class="text-danger">Tidak ada data pada master proses dyeing, proses tidak bisa dijalankan</p>
        <?php endif;?>
    </div>

    <div class="box-footer">
        <p>
            <?=Html::a('Catatan Proses', ['add-catatan-proses', 'id'=>$model->id], [
                'class' => 'btn btn-default btn-sm',
                'title' => 'Set Catatan Proses',
                'data-toggle'=>"modal",
                'data-target'=>"#processingDyeingModal",
                'data-title' => 'Set Catatan Proses'
            ])?>

            <?=Html::a('Hasil Tes Gosok', ['add-hasil-tes-gosok', 'id'=>$model->id], [
                'class' => 'btn btn-default btn-sm',
                'title' => 'Set Hasil Tes Gosok',
                'data-toggle'=>"modal",
                'data-target'=>"#processingDyeingModal",
                'data-title' => 'Set Hasil Tes Gosok'
            ])?>
        </p>
    </div>
</div>

<?=$this->render('proses_ulang', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);?>

<?php
echo AjaxModal::widget([
    'id' => 'processingDyeingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

JqueryUiAsset::register($this);
$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/child/js/proses.js');
$this->registerJs($js, $this::POS_END);

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

    $(document).on('click', '.toping-header-row', function() {
        $('.toping-row').toggle();
        var icon = $('#toping-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });

    $(document).on('click', '.rc-header-row', function() {
        $('.rc-row').toggle();
        var icon = $('#rc-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });

    $(document).on('click', '.rfulang-header-row', function() {
        $('.rfulang-row').toggle();
        var icon = $('#rfulang-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });

    $(document).on('click', '.toping-level-header-row', function() {
        $('.toping-level-row').toggle();
        var icon = $('#toping-level-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });

    $(document).on('click', '.leveling-header-row', function() {
        $('.leveling-row').toggle();
        var icon = $('#leveling-icon');
        if (icon.hasClass('glyphicon-chevron-down')) {
            icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        } else {
            icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
    });
");