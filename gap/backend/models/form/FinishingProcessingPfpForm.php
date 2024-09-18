<?php

namespace backend\models\form;

use yii\base\Model;

/**
*/
class FinishingProcessingPfpForm extends Model
{
    public $qty;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            ['qty', 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'qty' => 'Jumlah'
        ];
    }
}