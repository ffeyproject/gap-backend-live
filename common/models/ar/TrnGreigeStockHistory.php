<?php

namespace common\models\ar;

use Yii;
use common\models\User;

/**
 * This is the model class for table "trn_greige_stock_history".
 *
 * @property int $id
 * @property int $greige_id
 * @property float|null $gap_old
 * @property float|null $gap_new
 * @property float|null $stock_old
 * @property float|null $stock_new
 * @property float|null $available_old
 * @property float|null $available_new
 * @property float|null $booked_wo_old
 * @property float|null $booked_wo_new
 * @property float|null $stock_pfp_old
 * @property float|null $stock_pfp_new
 * @property float|null $stock_wip_old
 * @property float|null $stock_wip_new
 * @property float|null $stock_ef_old
 * @property float|null $stock_ef_new
 * @property float|null $booked_old
 * @property float|null $booked_new
 * @property float|null $booked_pfp_old
 * @property float|null $booked_pfp_new
 * @property float|null $booked_wip_old
 * @property float|null $booked_wip_new
 * @property float|null $booked_ef_old
 * @property float|null $booked_ef_new
 * @property float|null $booked_opfp_old
 * @property float|null $booked_opfp_new
 * @property float|null $available_pfp_old
 * @property float|null $available_pfp_new
 * @property float|null $stock_opname_old
 * @property float|null $stock_opname_new
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
            [
                [
                    'gap_old', 'gap_new',
                    'stock_old', 'stock_new',
                    'available_old', 'available_new',
                    'booked_wo_old', 'booked_wo_new',
                    'stock_pfp_old', 'stock_pfp_new',
                    'stock_wip_old', 'stock_wip_new',
                    'stock_ef_old', 'stock_ef_new',
                    'booked_old', 'booked_new',
                    'booked_pfp_old', 'booked_pfp_new',
                    'booked_wip_old', 'booked_wip_new',
                    'booked_ef_old', 'booked_ef_new',
                    'booked_opfp_old', 'booked_opfp_new',
                    'available_pfp_old', 'available_pfp_new',
                    'stock_opname_old', 'stock_opname_new'
                ], 'number'
            ],
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
            'gap_old' => 'Gap Lama',
            'gap_new' => 'Gap Baru',
            'stock_old' => 'Stock Lama',
            'stock_new' => 'Stock Baru',
            'available_old' => 'Available Lama',
            'available_new' => 'Available Baru',
            'booked_wo_old' => 'Booked WO Lama',
            'booked_wo_new' => 'Booked WO Baru',
            'stock_pfp_old' => 'Stock PFP Lama',
            'stock_pfp_new' => 'Stock PFP Baru',
            'stock_wip_old' => 'Stock WIP Lama',
            'stock_wip_new' => 'Stock WIP Baru',
            'stock_ef_old' => 'Stock EF Lama',
            'stock_ef_new' => 'Stock EF Baru',
            'booked_old' => 'Booked Lama',
            'booked_new' => 'Booked Baru',
            'booked_pfp_old' => 'Booked PFP Lama',
            'booked_pfp_new' => 'Booked PFP Baru',
            'booked_wip_old' => 'Booked WIP Lama',
            'booked_wip_new' => 'Booked WIP Baru',
            'booked_ef_old' => 'Booked EF Lama',
            'booked_ef_new' => 'Booked EF Baru',
            'booked_opfp_old' => 'Booked OPFP Lama',
            'booked_opfp_new' => 'Booked OPFP Baru',
            'available_pfp_old' => 'Available PFP Lama',
            'available_pfp_new' => 'Available PFP Baru',
            'stock_opname_old' => 'Stock Opname Lama',
            'stock_opname_new' => 'Stock Opname Baru',
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
