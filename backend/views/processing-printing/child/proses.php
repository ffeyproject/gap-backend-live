<?php

use backend\assets\JqueryUiAsset;
use backend\components\ajax_modal\AjaxModal;
use common\models\ar\KartuProcessPrintingProcess;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrinting;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPrinting[]*/
/* @var $processesUlang array*/
/* @var $formatter \yii\i18n\Formatter */

$this->registerCss('.ctn-disable{background-color:black;}');
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
                        <th>Ulang</th>
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
                                            $lblBtn = '<span class="label label-success">Set</span>';
                                        }else{
                                            $datas = Json::decode($pcModel->value);
                                            if(isset($datas[$key]) && !empty($datas[$key])){
                                                $lblBtn = $datas[$key].' <span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>';
                                            }else{
                                                $lblBtn = '<span class="label label-success">Set</span>';
                                            }
                                        }

                                        $label = $item->getAttributeLabel($key);

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
                                }else{
                                    echo '<td>'.$value.'</td>';
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
            <?php else:?>
                <p class="text-danger">Tidak ada data pada master proses printing, proses tidak bisa dijalankan</p>
            <?php endif;?>
        </div>

        <div class="box-footer">
            <p>
                <?=Html::a('Catatan Proses', ['add-catatan-proses', 'id'=>$model->id], [
                    'class' => 'btn btn-default btn-sm',
                    'title' => 'Set Catatan Proses',
                    'data-toggle'=>"modal",
                    'data-target'=>"#processingPrintingModal",
                    'data-title' => 'Set Catatan Proses'
                ])?>
            </p>
        </div>
    </div>

<?=$this->render('proses_ulang', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);?>

<?php
echo AjaxModal::widget([
    'id' => 'processingPrintingModal',
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