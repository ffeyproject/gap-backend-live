<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_kirim_buyer_item".
 *
 * @property int $id
 * @property int $kirim_buyer_id
 * @property int $stock_id
 * @property int $qty
 * @property string|null $note
 *
 * @property TrnGudangJadi $stock
 * @property TrnKirimBuyer $kirimBuyer
 */
class TrnKirimBuyerItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_buyer_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kirim_buyer_id', 'stock_id', 'qty'], 'required'],
            [['kirim_buyer_id', 'stock_id', 'qty'], 'default', 'value' => null],
            [['kirim_buyer_id', 'stock_id', 'qty'], 'integer'],
            [['note'], 'string'],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['stock_id' => 'id']],
            [['kirim_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimBuyer::className(), 'targetAttribute' => ['kirim_buyer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kirim_buyer_id' => 'Kirim Buyer ID',
            'stock_id' => 'Stock ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'stock_id']);
    }

    /**
     * Gets query for [[KirimBuyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKirimBuyer()
    {
        return $this->hasOne(TrnKirimBuyer::className(), ['id' => 'kirim_buyer_id']);
    }

    public function getStockGrade()
    {
        return $this->stock->grade;
    }
}
