<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mutasi_ex_finish_alt_item".
 *
 * @property int $id
 * @property int $mutasi_id
 * @property int $gudang_jadi_id
 * @property int $grade mengacu kepada TrnStockGreige::gradeOptions()
 * @property float $qty
 * @property int $status 1=Stock, 2=Dijual
 *
 * @property MutasiExFinishAlt $mutasi
 * @property TrnGudangJadi $gudangJadi
 * @property string $statusName
 */
class MutasiExFinishAltItem extends \yii\db\ActiveRecord
{
    const STATUS_STOCK = 1;
    const STATUS_DIJUAL = 2;

    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_STOCK => 'Stock',
            self::STATUS_DIJUAL => 'Dijual',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName(){
        return self::statusOptions()[$this->status];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_ex_finish_alt_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mutasi_id', 'gudang_jadi_id', 'grade', 'qty'], 'required'],
            [['mutasi_id', 'gudang_jadi_id', 'grade'], 'default', 'value' => null],
            [['mutasi_id', 'gudang_jadi_id', 'grade'], 'integer'],
            [['qty'], 'number'],
            [['mutasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasiExFinishAlt::className(), 'targetAttribute' => ['mutasi_id' => 'id']],
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
            'mutasi_id' => 'Mutasi ID',
            'gudang_jadi_id' => 'Gudang Jadi ID',
            'grade' => 'Grade',
            'qty' => 'Qty',
            'statusName' => 'Status',
        ];
    }

    /**
     * Gets query for [[Mutasi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasi()
    {
        return $this->hasOne(MutasiExFinishAlt::className(), ['id' => 'mutasi_id']);
    }

    /**
     * Gets query for [[GudangJadi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadi()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'gudang_jadi_id']);
    }
}
