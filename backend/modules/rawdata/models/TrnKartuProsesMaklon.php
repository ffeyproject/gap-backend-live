<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_kartu_proses_maklon".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int $vendor_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property int $status 1=draft, 2=posted, 3=approved (disetujui untuk dikirim ke vendor / memotong stock greige), 4=inspected (sudah diinspect), 5=Batal
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 *
 * @property MstVendor $vendor
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
 */
class TrnKartuProsesMaklon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_maklon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'date', 'status', 'created_at', 'created_by', 'unit'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'unit'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'unit'], 'integer'],
            [['note'], 'string'],
            [['date'], 'safe'],
            [['no'], 'string', 'max' => 255],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstVendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
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
            'vendor_id' => 'Vendor ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'note' => 'Note',
            'date' => 'Date',
            'posted_at' => 'Posted At',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'unit' => 'Unit',
        ];
    }

    /**
     * Gets query for [[Vendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(MstVendor::className(), ['id' => 'vendor_id']);
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

    /**
     * Gets query for [[TrnKartuProsesMaklonItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['kartu_process_id' => 'id']);
    }
}
