<?php

namespace backend\modules\rawdata\models;

use common\models\ar\TrnGudangJadi;
use Yii;

/**
 * This is the model class for table "trn_kirim_buyer_item".
 *
 * @property int $id
 * @property int $kirim_buyer_id
 * @property int $stock_id
 * @property string $qty
 * @property string $note
 * @property string $no_bal
 * @property int $bal_id
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
            [['kirim_buyer_id', 'stock_id', 'bal_id'], 'integer'],
            [['qty'], 'number'],
            [['note'], 'string'],
            [['no_bal'], 'string', 'max' => 45],
            [['kirim_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimBuyer::className(), 'targetAttribute' => ['kirim_buyer_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['stock_id' => 'id']],
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
            'no_bal' => 'No Bal',
            'bal_id' => 'Bal ID',
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

    public function getNoLot()
{
    if (!$this->stock || !$this->stock->source_ref) {
        return '-';
    }

    $sourceRef = $this->stock->source_ref;

    $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
        ->select('no_lot')
        ->where(['no' => $sourceRef])
        ->scalar();

    if ($noLot) {
        return $noLot;
    }

    $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
        ->select('no_lot')
        ->where(['no' => $sourceRef])
        ->scalar();

    return $noLot ?: '-';
}

}