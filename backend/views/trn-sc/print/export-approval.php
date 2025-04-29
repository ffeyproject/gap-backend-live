<?php

use common\models\ar\TrnSc;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnSc */


$formatter = Yii::$app->formatter;
//$formatter->locale = 'en_GB';
$bankAccount = $model->bankAcct;
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>EXPORT SALES CONTRACT</h4>
        <strong>Sales Contract No: <?=$model->no?></strong>
    </div>
</div>

<br>

<p>This contract manufacturing and sales of good made this day : <?=$formatter->asDate($model->date, 'long')?>.</p>

<p><?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table detail-view small'],
        'template' => '<tr><td style="width: 25%;">{label}</td><th{contentOptions}>: {value}</td></tr>',
        'attributes' => [
            [
                'label' => 'By',
                'value' => 'PT. GAJAH ANGKASA PERKASA'
            ],
            [
                'label' => 'And Between',
                'format' => 'html',
                'value' => $model->customerName
            ],
            [
                'label' => '',
                'value' => $model->destination
            ]
        ],
    ]) ?></p>

<p>For the purchase of goods describe below :</p>

<table class="table table-bordered small">
    <tr>
        <th colspan="2" class="text-center">Description</th>
        <th class="text-center">Grade</th>
        <th class="text-center">Piece Length</th>
        <th class="text-center">Qty</th>
        <th class="text-center">Price (<?=$model::currencyOptions()[$model->currency]?>)</th>
        <th class="text-center">Total Finish</th>
        <th class="text-center">Qty</th>
    </tr>
    <?php
    $trnScGreiges = $model->trnScGreiges;
    ?>

    <?php foreach ($trnScGreiges as $trnScGreige):?>
    <tr>
        <td style="text-align: center">
            <?=$trnScGreige->greigeGroup->nama_kain?>
        </td>
        <td style="text-align: center">
            <?php
                if(is_null($trnScGreige->artikel_sc)){
                    echo '-';
                }else{
                    echo $trnScGreige->artikel_sc;
                }
            ?>
        </td>
        <!-- <td style="text-align: center"> -->
        <!--<?=$trnScGreige->artikel_sc?> -->
        <!-- </td> -->
        <td style="text-align: center"><?=$trnScGreige::processOptions()[$trnScGreige->process]?> /
            <?=$trnScGreige::lebarKainOptions()[$trnScGreige->lebar_kain]?>"</td>
        <td style="text-align: center"><?=$trnScGreige->grade?></td>
        <td style="text-align: center"><?=$trnScGreige->piece_length?></td>
        <td style="text-align: right"><?=$formatter->asDecimal($trnScGreige->qty)?></td>
        <td style="text-align: center"><?=$formatter->asDecimal($trnScGreige->unit_price, 4)?></td>
        <td style="text-align: center">
            <?php
                switch ($trnScGreige->price_param){
                    case $trnScGreige::PRICE_PARAM_PER_YARD:
                        echo $formatter->asDecimal($trnScGreige->qtyFinishToYard);
                        break;
                    default:
                        echo $formatter->asDecimal($trnScGreige->qtyFinish);
                }
                ?>
        </td>
        <td style="text-align: center">
            <?php
                switch ($trnScGreige->price_param){
                    case $trnScGreige::PRICE_PARAM_PER_METER:
                        echo 'Meter';
                        break;
                    case $trnScGreige::PRICE_PARAM_PER_KILOGRAM:
                        echo 'Kilogram';
                        break;
                    case $trnScGreige::PRICE_PARAM_PER_YARD:
                        echo 'Yard';
                        break;
                    default:
                        echo '-';
                }
                ?>
        </td>
    </tr>
    <?php endforeach;?>
</table>

<strong>Note:</strong>
<?=$model->note?>

<strong>Remarks:</strong>

<p><?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table detail-view small'],
        'template' => '<tr><td style="width: 25%;">{label}</td><td{contentOptions}>: {value}</td></tr>',
        'attributes' => [
            'pmt_method',
            'pmt_term',
            [
                'label' => 'Delivery Term',
                'value' => $model::ongkosAngkutOptions()[$model->ongkos_angkut]
            ],
            'delivery_date',
            'consignee_name',
            'notify_party',
            'buyer_name_in_invoice',
        ],
    ]) ?></p>

<p>PLEASE KINDLY SEND THE TT TO BELOW ACCOUNT</p>

<?=$bankAccount->bank_name?>.
<br>
<?=$bankAccount->address?>.

<p><?= DetailView::widget([
        'model' => $bankAccount,
        'options' => ['class' => 'table detail-view small'],
        'template' => '<tr><td style="width: 25%;">{label}</td><td{contentOptions}>: {value}</td></tr>',
        'attributes' => [
            'acct_no',
            'acct_name',
            'swift_code',
        ],
    ]) ?></p>

<?php
$corespondenceStr = '<ol>';
foreach (explode(',', $bankAccount->correspondence) as $str){
    $corespondenceStr .= '<li>'.$str.'</li>';
}

$corespondenceStr .= '</ol>';
?>
Correspondence <?=ucwords(strtolower($bankAccount->bank_name))?> :<br>
<?=$corespondenceStr?>

<br>

<p>PS : ALL BANK CHARGES shall not be bear by PT. GAJAH ANGKASA PERKASA.</p>

<br>

<table class="table">
    <tr>
        <td style="width: 33.3%; text-align: center;">
            Buyer,<br><br><br><br><br><br>(<?=$model->customerName?>)
        </td>
        <td style="width: 33.3%; text-align: center;">
            Manager,<br>
            <?= Html::img($model->manager->signatureUrl, ['style'=>'height:100px;'])?><br>
            (<?=$model->managerName?>)
        </td>
        <td style="text-align: center;">
            Marketing,<br>
            <?= Html::img($model->marketing->signatureUrl, ['style'=>'height:100px;'])?><br>
            (<?=$model->marketingName?>)
        </td>
    </tr>
</table>

<br>

<strong>ATTENTION :</strong>

<br>

THIS CONTRACT IS NOT VALID IF :
<br>1. BUYER HAS NOT SIGN IN 7 DAYS.
<br>2. BUYER HAS NOT GIVE INSTRUCTION/DESIGN/COLOUR IN 30 DAYS.