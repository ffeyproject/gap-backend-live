<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_kartu_proses_celup_item".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $order_celup_id
 * @property int $kartu_process_id
 * @property int $stock_id
 * @property int $panjang_m
 * @property string|null $mesin
 * @property int $tube 1=kiri, 2=kanan
 * @property string|null $note
 * @property int $status 1=Pending, 2=Valid
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnKartuProsesCelup $kartuProcess
 * @property TrnOrderCelup $orderCelup
 * @property TrnStockGreige $stock
 */
class TrnKartuProsesCelupItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_celup_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'order_celup_id', 'kartu_process_id', 'stock_id', 'panjang_m'], 'required'],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status'], 'integer'],
            [['note'], 'string'],
            [['mesin'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesCelup::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
            [['order_celup_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnOrderCelup::className(), 'targetAttribute' => ['order_celup_id' => 'id']],
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
            'order_celup_id' => 'Order Celup ID',
            'kartu_process_id' => 'Kartu Process ID',
            'stock_id' => 'Stock ID',
            'panjang_m' => 'Panjang M',
            'mesin' => 'Mesin',
            'tube' => 'Tube',
            'note' => 'Note',
            'status' => 'Status',
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
     * Gets query for [[GreigeGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesCelup::className(), ['id' => 'kartu_process_id']);
    }

    /**
     * Gets query for [[OrderCelup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderCelup()
    {
        return $this->hasOne(TrnOrderCelup::className(), ['id' => 'order_celup_id']);
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_id']);
    }
}
