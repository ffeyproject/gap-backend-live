<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_potong_stock_item".
 *
 * @property int $id
 * @property int $potong_stock_id
 * @property float $qty
 *
 * @property TrnPotongStock $potongStock
 */
class TrnPotongStockItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_potong_stock_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            [['potong_stock_id'], 'default', 'value' => null],
            [['potong_stock_id'], 'integer'],
            [['qty'], 'number'],
            [['potong_stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnPotongStock::className(), 'targetAttribute' => ['potong_stock_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'potong_stock_id' => 'Potong Stock ID',
            'qty' => 'Qty',
        ];
    }

    /**
     * Gets query for [[PotongStock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPotongStock()
    {
        return $this->hasOne(TrnPotongStock::className(), ['id' => 'potong_stock_id']);
    }
}
