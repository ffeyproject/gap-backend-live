<?php

namespace common\models\ar;

use Yii;
use common\models\User;

/**
 * This is the model class for table "trn_greige_stock_history".
 *
 * @property int $id
 * @property int $greige_id
 * @property float|null $stock_old
 * @property float|null $stock_new
 * @property float|null $available_old
 * @property float|null $available_new
 * @property float|null $booked_wo_old
 * @property float|null $booked_wo_new
 * @property float|null $booked_pfp_old
 * @property float|null $booked_pfp_new
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $context
 *
 * @property MstGreige $greige
 * @property User $createdBy
 */
class TrnGreigeStockHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_greige_stock_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_id'], 'required'],
            [['greige_id', 'created_by'], 'default', 'value' => null],
            [['greige_id', 'created_by'], 'integer'],
            [['stock_old', 'stock_new', 'available_old', 'available_new', 'booked_wo_old', 'booked_wo_new', 'booked_pfp_old', 'booked_pfp_new'], 'number'],
            [['created_at'], 'safe'],
            [['context'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_id' => 'Greige',
            'stock_old' => 'Stock Lama',
            'stock_new' => 'Stock Baru',
            'available_old' => 'Available Lama',
            'available_new' => 'Available Baru',
            'booked_wo_old' => 'Booked WO Lama',
            'booked_wo_new' => 'Booked WO Baru',
            'booked_pfp_old' => 'Booked PFP Lama',
            'booked_pfp_new' => 'Booked PFP Baru',
            'created_at' => 'Waktu',
            'created_by' => 'Oleh',
            'context' => 'Konteks / Transaksi',
        ];
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
