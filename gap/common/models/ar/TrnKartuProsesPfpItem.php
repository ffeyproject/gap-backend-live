<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_kartu_proses_pfp_item".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $order_pfp_id
 * @property int $kartu_process_id
 * @property int $stock_id
 * @property int $panjang_m
 * @property string|null $mesin
 * @property int $tube 1=kiri, 2=kanan
 * @property string|null $note
 * @property int $status 1=Pending, 2=Valid
 * @property string $date
 * @property int $created_at
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnKartuProsesPfp $kartuProcess
 * @property TrnOrderPfp $orderPfp
 * @property TrnStockGreige $stock
 */
class TrnKartuProsesPfpItem extends \yii\db\ActiveRecord
{
    const TUBE_KIRI = 1;const TUBE_KANAN = 2;
    /**
     * @return array
     */
    public static function tubeOptions(){
        return [self::TUBE_KIRI => 'Tube Kiri', self::TUBE_KANAN => 'Tube Kanan',];
    }

    const STATUS_PENDING = 1;const STATUS_VALID = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_PENDING => 'Pending', self::STATUS_VALID => 'Valid',];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_pfp_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'stock_id', 'date', 'created_at', 'tube'], 'required'],
            [['greige_group_id', 'greige_id', 'order_pfp_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status', 'created_at'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'order_pfp_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status', 'created_at'], 'integer'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],
            [['stock_id', 'kartu_process_id'], 'unique', 'targetAttribute' => ['stock_id', 'kartu_process_id']],
            ['status', 'default', 'value'=>self::STATUS_PENDING],
            [['mesin'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesPfp::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
            [['order_pfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnOrderPfp::className(), 'targetAttribute' => ['order_pfp_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'order_pfp_id' => 'Order Pfp ID',
            'kartu_process_id' => 'Kartu Process ID',
            'stock_id' => 'Stock ID',
            'panjang_m' => 'Panjang M',
            'mesin' => 'Mesin',
            'tube' => 'Tube',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesPfp::className(), ['id' => 'kartu_process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderPfp()
    {
        return $this->hasOne(TrnOrderPfp::className(), ['id' => 'order_pfp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_id']);
    }
}
