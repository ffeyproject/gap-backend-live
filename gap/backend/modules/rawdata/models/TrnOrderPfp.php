<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_order_pfp".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property float $qty
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted, 3=Processed
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $handling_id
 * @property int $approved_by
 * @property int|null $approved_at
 * @property string|null $approval_note
 * @property int|null $proses_sampai 1=Sampai Preset, 2=Sampai Setting
 * @property string|null $dasar_warna
 *
 * @property TrnKartuProsesPfp[] $trnKartuProsesPfps
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MstHandling $handling
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $approvedBy
 */
class TrnOrderPfp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_order_pfp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'qty', 'date', 'created_at', 'created_by', 'handling_id'], 'required'],
            [['greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'approved_by', 'approved_at', 'proses_sampai'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'approved_by', 'approved_at', 'proses_sampai'], 'integer'],
            [['qty'], 'number'],
            [['note', 'approval_note'], 'string'],
            [['date'], 'safe'],
            [['no', 'dasar_warna'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'qty' => 'Qty',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'handling_id' => 'Handling ID',
            'approved_by' => 'Approved By',
            'approved_at' => 'Approved At',
            'approval_note' => 'Approval Note',
            'proses_sampai' => 'Proses Sampai',
            'dasar_warna' => 'Dasar Warna',
        ];
    }

    /**
     * Gets query for [[TrnKartuProsesPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfps()
    {
        return $this->hasMany(TrnKartuProsesPfp::className(), ['order_pfp_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['order_pfp_id' => 'id']);
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[GreigeGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * Gets query for [[Handling]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandling()
    {
        return $this->hasOne(MstHandling::className(), ['id' => 'handling_id']);
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
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }
}
