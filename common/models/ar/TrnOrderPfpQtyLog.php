<?php
namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

class TrnOrderPfpQtyLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%trn_order_pfp_qty_log}}';
    }

    public function rules()
    {
        return [
            [['order_pfp_id', 'qty_tambah', 'total_meter', 'created_at'], 'required'],
            [['order_pfp_id', 'user_id', 'created_at'], 'integer'],
            [['qty_tambah', 'total_meter'], 'number'],
            [['keterangan'], 'string', 'max' => 255],
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(TrnOrderPfp::class, ['id' => 'order_pfp_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'user_id']);
    }
}