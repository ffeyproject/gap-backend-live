<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_greige_keluar_item".
 *
 * @property int $greige_keluar_id
 * @property int $stock_greige_id
 * @property string|null $note
 *
 * @property TrnGreigeKeluar $greigeKeluar
 * @property TrnStockGreige $stockGreige
 */
class TrnGreigeKeluarItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_greige_keluar_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_greige_id'], 'required'],
            [['greige_keluar_id', 'stock_greige_id'], 'default', 'value' => null],
            [['greige_keluar_id', 'stock_greige_id'], 'integer'],
            [['note'], 'string'],
            [['greige_keluar_id', 'stock_greige_id'], 'unique', 'targetAttribute' => ['greige_keluar_id', 'stock_greige_id']],
            [['greige_keluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGreigeKeluar::className(), 'targetAttribute' => ['greige_keluar_id' => 'id']],
            [
                'stock_greige_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => TrnStockGreige::className(),
                'targetAttribute' => ['stock_greige_id' => 'id'],
                'filter' => ['status'=>TrnStockGreige::STATUS_VALID]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'greige_keluar_id' => 'Greige Keluar ID',
            'stock_greige_id' => 'Stock Greige ID',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[GreigeKeluar]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeKeluar()
    {
        return $this->hasOne(TrnGreigeKeluar::className(), ['id' => 'greige_keluar_id']);
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
