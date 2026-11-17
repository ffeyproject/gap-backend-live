<?php

namespace backend\models\form;

use common\models\ar\TrnStockGreige;
use yii\base\Model;

/**
*/
class FinishingProcessingPfpForm extends Model
{
    public $qty;
    public $jenis_printing;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'jenis_printing'], 'required'],
            ['qty', 'number'],
            ['jenis_printing', 'in', 'range' => [
                TrnStockGreige::JENIS_PRINTING_DIGITAL,
                TrnStockGreige::JENIS_PRINTING_KONVENSIONAL,
            ]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'qty' => 'Jumlah',
            'jenis_printing' => 'Jenis Printing'
        ];
    }
}
