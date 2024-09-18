<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_mixed_greige_item".
 *
 * @property int $mix_id
 * @property int $stock_greige_id
 *
 * @property TrnMixedGreige $mix
 */
class TrnMixedGreigeItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mixed_greige_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mix_id', 'stock_greige_id'], 'required'],
            [['mix_id', 'stock_greige_id'], 'default', 'value' => null],
            [['mix_id', 'stock_greige_id'], 'integer'],
            [['mix_id', 'stock_greige_id'], 'unique', 'targetAttribute' => ['mix_id', 'stock_greige_id']],
            [['mix_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMixedGreige::className(), 'targetAttribute' => ['mix_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mix_id' => 'Mix ID',
            'stock_greige_id' => 'Stock Greige ID',
        ];
    }

    /**
     * Gets query for [[Mix]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMix()
    {
        return $this->hasOne(TrnMixedGreige::className(), ['id' => 'mix_id']);
    }
}
