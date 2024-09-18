<?php
namespace backend\models\ar;

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnStockGreige;

class StockGreige extends TrnStockGreige
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panjang_m', 'grade'], 'required'],
            ['no_set_lusi', 'default', 'value'=>'-'],
            [['greige_group_id', 'greige_id', 'asal_greige', 'grade', 'panjang_m', 'status_tsd', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'asal_greige', 'grade', 'status_tsd', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['panjang_m', 'number'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_PENDING],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'no_document', 'pengirim', 'mengetahui'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
        ];
    }
}