<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_potong_greige_item".
 *
 * @property int $id
 * @property int|null $potong_greige_id
 * @property int|null $stock_greige_id
 * @property float $panjang_m
 *
 * @property TrnPotongGreige $potongGreige
 * @property TrnStockGreige $stockGreige
 */
class TrnPotongGreigeItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_potong_greige_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['potong_greige_id', 'stock_greige_id', 'panjang_m'], 'default', 'value' => null],
            [['potong_greige_id', 'stock_greige_id'], 'integer'],
            ['panjang_m', 'number'],
            [['panjang_m'], 'required'],
            [['potong_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnPotongGreige::className(), 'targetAttribute' => ['potong_greige_id' => 'id']],
            [['stock_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'potong_greige_id' => 'Potong Greige ID',
            'stock_greige_id' => 'Stock Greige ID',
            'panjang_m' => 'Panjang M',
        ];
    }

    /**
     * Gets query for [[PotongGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPotongGreige()
    {
        return $this->hasOne(TrnPotongGreige::className(), ['id' => 'potong_greige_id']);
    }

    /**
     * Gets query for [[StockGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockGreige()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_greige_id']);
    }
}
