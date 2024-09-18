<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_kirim_makloon_item".
 *
 * @property int $id
 * @property int $kirim_makloon_id
 * @property int $stock_id
 * @property int $qty
 * @property string|null $note
 *
 * @property TrnGudangJadi $stock
 * @property TrnKirimMakloon $kirimMakloon
 */
class TrnKirimMakloonItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_makloon_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kirim_makloon_id', 'stock_id', 'qty'], 'required'],
            [['kirim_makloon_id', 'stock_id', 'qty'], 'default', 'value' => null],
            [['kirim_makloon_id', 'stock_id', 'qty'], 'integer'],
            [['note'], 'string'],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['stock_id' => 'id']],
            [['kirim_makloon_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimMakloon::className(), 'targetAttribute' => ['kirim_makloon_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kirim_makloon_id' => 'Kirim Makloon ID',
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
     * Gets query for [[KirimMakloon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKirimMakloon()
    {
        return $this->hasOne(TrnKirimMakloon::className(), ['id' => 'kirim_makloon_id']);
    }
}
