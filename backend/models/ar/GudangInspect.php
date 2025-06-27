<?php
namespace backend\models\ar;

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangInspect;

class GudangInspect extends TrnGudangInspect
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'asal_greige','status_tsd', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'asal_greige', 'status_tsd', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by','jenis_beli'], 'integer'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_document', 'operator', 'mengetahui'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
        ];
    }
}