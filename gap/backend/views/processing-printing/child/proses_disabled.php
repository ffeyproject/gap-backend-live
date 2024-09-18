<?php

use common\models\ar\KartuProcessPrintingProcess;
use common\models\ar\MstProcessPrinting;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\i18n\Formatter;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPrinting */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPrinting[]*/
/* @var $processesUlang array*/
/* @var $formatter Formatter */

$this->registerCss('.ctn-disable{background-color:black;}');
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
                    foreach ($processModels as $item){
                        echo '<tr>';
                        foreach ($item->attributes as $key=>$value){
                            if(!in_array($key, ['id', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by', 'max_pengulangan'])){
                                if($key !== 'nama_proses'){
                                    if($value){
                                        echo '<td>';

                                        $pcModel = KartuProcessPrintingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$item->id]);
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
            <?php else:?>
                <p class="text-danger">Tidak ada data pada master proses printing, proses tidak bisa dijalankan</p>
            <?php endif;?>
        </div>

        <div class="box-footer"></div>
    </div>

<?=$this->render('proses_ulang', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);?>