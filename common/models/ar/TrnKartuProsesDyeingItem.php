<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_kartu_proses_dyeing_item".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int $kartu_process_id
 * @property int $stock_id
 * @property int $panjang_m
 * @property string|null $mesin
 * @property int $tube 1=kiri, 2=kanan
 * @property string|null $note
 * @property int $status
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnKartuProsesDyeing $kartuProcess
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnStockGreige $stock
 * @property TrnWo $wo
 * @property User $createdBy
 * @property User $updatedBy
 */
class TrnKartuProsesDyeingItem extends \yii\db\ActiveRecord
{
    const TUBE_KIRI = 1;const TUBE_KANAN = 2;
    /**
     * @return array
     */
    public static function tubeOptions(){
        return [self::TUBE_KIRI => 'Tube Kiri', self::TUBE_KANAN => 'Tube Kanan',];
    }

    const STATUS_PENDING = 1;const STATUS_VALID = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_PENDING => 'Pending', self::STATUS_VALID => 'Valid',];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_dyeing_item';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'tube', 'date'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'tube', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'tube', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['panjang_m'], 'number'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],
            ['stock_id', 'unique', 'targetAttribute' => ['stock_id', 'kartu_process_id']],
            [['mesin'], 'string', 'max' => 255],
            ['status', 'default', 'value'=>self::STATUS_PENDING],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesDyeing::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'kartu_process_id' => 'Kartu Process ID',
            'stock_id' => 'Stock ID',
            'panjang_m' => 'Panjang (M)',
            'mesin' => 'Mesin',
            'tube' => 'Tube',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getKartuProses()
{
    return $this->hasOne(TrnKartuProsesDyeing::class, ['id' => 'trn_kartu_proses_dyeing_id']);
}

public function getMstGreige()
{
    return $this->hasOne(MstGreige::class, ['id' => 'greige_id']);
}


}