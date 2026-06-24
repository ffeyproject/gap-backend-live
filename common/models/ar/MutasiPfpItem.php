<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mutasi_pfp_item".
 *
 * @property int $id
 * @property int $mutasi_id
 * @property int $stock_pfp_id
 * @property string|null $note
 *
 * @property MutasiPfp $mutasi
 * @property TrnStockGreige $stockPfp
 */
class MutasiPfpItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_pfp_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mutasi_id', 'stock_pfp_id'], 'required'],
            [['mutasi_id', 'stock_pfp_id'], 'default', 'value' => null],
            [['mutasi_id', 'stock_pfp_id'], 'integer'],
            [['note'], 'string'],
            [['mutasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasiPfp::className(), 'targetAttribute' => ['mutasi_id' => 'id']],
            [['stock_pfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_pfp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mutasi_id' => 'Mutasi ID',
            'stock_pfp_id' => 'Stock Pfp ID',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Mutasi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasi()
    {
        return $this->hasOne(MutasiPfp::className(), ['id' => 'mutasi_id']);
    }

    /**
     * Gets query for [[StockPfp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockPfp()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_pfp_id']);
    }
}
