<?php
namespace backend\models\ar;

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangInspectItem;

class GudangInspectItem extends TrnGudangInspectItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panjang_m', 'grade'], 'required'],
            ['no_set_lusi', 'default', 'value'=>'-'],
            [['grade', 'panjang_m'], 'default', 'value' => null],
            [['grade', 'created_at','updated_at'], 'integer'],
            ['panjang_m', 'number'],
            [['no_set_lusi'], 'string', 'max' => 255],
            [['is_out'], 'boolean'],
        ];
    }
}