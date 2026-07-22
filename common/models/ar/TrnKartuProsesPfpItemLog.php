<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\User;

/**
 * This is the model class for table "trn_kartu_proses_pfp_item_log".
 *
 * @property int $id
 * @property int $kartu_process_id
 * @property int|null $item_id
 * @property int|null $stock_id
 * @property int $action_type
 * @property float|null $qty_before
 * @property float|null $qty_after
 * @property string|null $alasan
 * @property int $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnKartuProsesPfp $kartuProcess
 * @property TrnStockGreige $stock
 * @property User $createdBy
 * @property User $updatedBy
 */
class TrnKartuProsesPfpItemLog extends \yii\db\ActiveRecord
{
    const ACTION_TAMBAH   = 1;
    const ACTION_HAPUS    = 2;
    const ACTION_UBAH_QTY = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_pfp_item_log';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return array
     */
    public static function actionOptions()
    {
        return [
            self::ACTION_TAMBAH   => 'Tambah Roll',
            self::ACTION_HAPUS    => 'Hapus Roll',
            self::ACTION_UBAH_QTY => 'Ubah Qty Roll',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'action_type'], 'required'],
            [['kartu_process_id', 'item_id', 'stock_id', 'created_by', 'updated_by', 'action_type'], 'integer'],
            [['qty_before', 'qty_after'], 'number'],
            [['alasan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['action_type', 'in', 'range' => [self::ACTION_TAMBAH, self::ACTION_HAPUS, self::ACTION_UBAH_QTY]],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesPfp::class, 'targetAttribute' => ['kartu_process_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::class, 'targetAttribute' => ['stock_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kartu_process_id' => 'Kartu Process ID',
            'item_id' => 'Item ID',
            'stock_id' => 'Stock ID',
            'action_type' => 'Action Type',
            'qty_before' => 'Qty Before',
            'qty_after' => 'Qty After',
            'alasan' => 'Alasan',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesPfp::class, ['id' => 'kartu_process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::class, ['id' => 'stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
