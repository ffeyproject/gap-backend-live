<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_retur_pengantar_item".
 *
 * @property int $id
 * @property int $pengantar_id
 * @property int $gudang_jadi_id Stok hasil retur (trn_gudang_jadi.id)
 * @property float $qty
 * @property int|null $stock_greige_id Stok Gudang Mutasi yang dibuat saat posting ke Processing
 *
 * @property TrnReturPengantar $pengantar
 * @property TrnGudangJadi $gudangJadi
 * @property TrnStockGreige $stockGreige
 */
class TrnReturPengantarItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_retur_pengantar_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gudang_jadi_id', 'qty'], 'required'],
            [['pengantar_id', 'gudang_jadi_id', 'stock_greige_id'], 'integer'],
            ['qty', 'number'],
            [['pengantar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnReturPengantar::className(), 'targetAttribute' => ['pengantar_id' => 'id']],
            [['gudang_jadi_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['gudang_jadi_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pengantar_id' => 'Surat Pengantar',
            'gudang_jadi_id' => 'Stok Retur',
            'qty' => 'Qty',
            'stock_greige_id' => 'Stok Gudang Mutasi',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengantar()
    {
        return $this->hasOne(TrnReturPengantar::className(), ['id' => 'pengantar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadi()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'gudang_jadi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockGreige()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_greige_id']);
    }
}
