<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

class TrnKartuProsesDyeingItemLog extends \yii\db\ActiveRecord
{
    const ACTION_TAMBAH   = 1;
    const ACTION_HAPUS    = 2;
    const ACTION_UBAH_QTY = 3;

    public static function tableName()
    {
        return 'trn_kartu_proses_dyeing_item_log';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    public static function actionOptions()
    {
        return [
            self::ACTION_TAMBAH   => 'Tambah Roll',
            self::ACTION_HAPUS    => 'Hapus Roll',
            self::ACTION_UBAH_QTY => 'Ubah Qty Roll',
        ];
    }

    public function rules()
    {
        return [
            [['kartu_process_id', 'action_type'], 'required'],
            [['kartu_process_id', 'item_id', 'stock_id', 'created_by', 'updated_by', 'action_type'], 'integer'],
            [['qty_before', 'qty_after'], 'number'],
            [['alasan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['action_type', 'in', 'range' => [1, 2, 3]],
        ];
    }
}