<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_buy_greige".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property string $no_document
 * @property string $vendor
 * @property string|null $note
 * @property int $jenis_beli 1=Beli Lokal, 2=Beli Import
 * @property int $status 1=Draft, 2=Posted, 3=Approved
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $approval_id
 * @property int|null $approval_time
 * @property string|null $reject_note
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnBuyGreigeItem[] $trnBuyGreigeItems
 *
 * @property string $greigeName
 * @property string $greigeGroupName
 * @property string $statusName
 * @property string $jenisBeliName
 */
class TrnBuyGreige extends \yii\db\ActiveRecord
{
    const JENIS_BELI_LOKAL = 1;const JENIS_BELI_IMPORT = 2;
    /**
     * @return array
     */
    public static function jenisBeliOptions(){
        return [self::JENIS_BELI_LOKAL => 'Beli Lokal', self::JENIS_BELI_IMPORT => 'Beli Import'];
    }

    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_buy_greige';
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
            [['greige_id', 'no_document', 'vendor', 'date'], 'required'],
            [['greige_group_id', 'greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'jenis_beli', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'integer'],
            [['note', 'reject_note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED]],
            ['jenis_beli', 'default', 'value'=>self::JENIS_BELI_LOKAL],
            ['jenis_beli', 'in', 'range' => [self::JENIS_BELI_LOKAL, self::JENIS_BELI_IMPORT]],
            [['no_document', 'vendor'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
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
            'no_document' => 'No Document',
            'vendor' => 'Vendor',
            'note' => 'Note',
            'jenis_beli' => 'Jenis Beli',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'approval_id' => 'Approval ID',
            'approval_time' => 'Approval Time',
            'reject_note' => 'Reject Note',
        ];
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
     * Gets query for [[TrnBuyGreigeItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyGreigeItems()
    {
        return $this->hasMany(TrnBuyGreigeItem::className(), ['buy_greige_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getGreigeName()
    {
        return $this->greige->nama_kain;
    }

    /**
     * @return string
     */
    public function getGreigeGroupName()
    {
        return $this->greigeGroup->nama_kain;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return self::statusOptions()[$this->status];
    }

    /**
     * @return string
     */
    public function getJenisBeliName()
    {
        return self::jenisBeliOptions()[$this->jenis_beli];
    }
}
