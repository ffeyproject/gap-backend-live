<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_pfp_keluar_item".
 *
 * @property int $pfp_keluar_id
 * @property int $stock_pfp_id
 * @property string|null $note
 *
 * @property TrnPfpKeluar $pfpKeluar
 * @property TrnStockGreige $stockPfp
 */
class TrnPfpKeluarItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_pfp_keluar_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pfp_keluar_id', 'stock_pfp_id'], 'required'],
            [['pfp_keluar_id', 'stock_pfp_id'], 'default', 'value' => null],
            [['pfp_keluar_id', 'stock_pfp_id'], 'integer'],
            [['note'], 'string'],
            [['pfp_keluar_id', 'stock_pfp_id'], 'unique', 'targetAttribute' => ['pfp_keluar_id', 'stock_pfp_id']],
            [['pfp_keluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnPfpKeluar::className(), 'targetAttribute' => ['pfp_keluar_id' => 'id']],
            [['stock_pfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_pfp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pfp_keluar_id' => 'Pfp Keluar ID',
            'stock_pfp_id' => 'Stock Pfp ID',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[PfpKeluar]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPfpKeluar()
    {
        return $this->hasOne(TrnPfpKeluar::className(), ['id' => 'pfp_keluar_id']);
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
