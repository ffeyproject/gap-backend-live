<?php

use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrinting;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPrinting[]*/
/* @var $processesUlang array*/
/* @var $formatter \yii\i18n\Formatter */
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><strong>Proses Ulang</strong></h3>
        <div class="box-tools pull-right"><span class="label label-info">Catatan pengulangan tiap2 proses</span></div>
    </div>
    <div class="box-body">
        <?php foreach ($processesUlang as $processUlang):?>
            <div class="panel panel-default">
                <div class="panel-heading"><?=$processUlang['nama_proses']?></div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <?php foreach ($processUlang['header'] as $header){echo '<th>'.$header.'</th>';}?>
                            <th>Memo</th>
                            <th>Waktu</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($processUlang['pengulangan'] as $pengulangan){
                            echo '<tr>';
                            foreach ($pengulangan['data'] as $datum) {
                                if($datum !== null){
                                    echo '<td>'.$datum.'</td>';
                                }else{
                                    echo '<td>'.$datum.'</td>';
                                }
                            }
                            echo '<td>'.$pengulangan['head']['memo'].'</td>';
                            echo '<td>'.$formatter->asDatetime($pengulangan['head']['time']).'</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>