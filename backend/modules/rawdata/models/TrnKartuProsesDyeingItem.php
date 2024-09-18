<?php

namespace backend\modules\rawdata\models;

use Yii;

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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_dyeing_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'date', 'created_at', 'created_by'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['note'], 'string'],
            [['date'], 'safe'],
            [['mesin'], 'string', 'max' => 255],
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
            'panjang_m' => 'Panjang M',
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
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
