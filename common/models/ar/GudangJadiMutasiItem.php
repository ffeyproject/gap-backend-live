<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "gudang_jadi_mutasi_item".
 *
 * @property int $id
 * @property int $mutasi_id
 * @property int $stock_id id stock gudang jadi.
 * @property string $note
 *
 * @property GudangJadiMutasi $mutasi
 * @property TrnGudangJadi $stock
 */
class GudangJadiMutasiItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gudang_jadi_mutasi_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_id'], 'required'],
            [['mutasi_id', 'stock_id'], 'default', 'value' => null],
            [['mutasi_id', 'stock_id'], 'integer'],
            [['note'], 'string'],
            [['mutasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => GudangJadiMutasi::className(), 'targetAttribute' => ['mutasi_id' => 'id']],
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
            'mutasi_id' => 'Mutasi ID',
            'stock_id' => 'Stock ID',
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
        return $this->hasOne(GudangJadiMutasi::className(), ['id' => 'mutasi_id']);
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
}
