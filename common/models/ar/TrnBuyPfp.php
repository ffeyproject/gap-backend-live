<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_buy_pfp".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property string $no_document
 * @property string $vendor
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted, 3=Approved
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $approval_id
 * @property int|null $approval_time
 * @property string|null $reject_note
 * @property string|null $color
 * @property int $jenis 1=Beli, 2=Hasil Makloon, 3=Lain-lain
 * @property string|null $no_referensi
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnBuyPfpItem[] $trnBuyPfpItems
 *
 * @property string $greigeName
 * @property string $greigeGroupName
 * @property string $statusName
 */
class TrnBuyPfp extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved'];
    }

    const JENIS_BELI = 1;const JENIS_MAKLOON = 2; const JENIS_LAIN_LAIN = 3;
    /**
     * @return array
     */
    public static function jenisOptions(){
        return [self::JENIS_BELI => 'Beli', self::JENIS_MAKLOON => 'Hasil Makloon', self::JENIS_LAIN_LAIN => 'Lain-lain'];
    }

    /**
     * @return string
    */
    public function getJenisName(){
        return self::jenisOptions()[$this->jenis];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_buy_pfp';
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
            [['greige_id', 'no_document', 'vendor', 'date', 'color', 'jenis'], 'required'],
            [['greige_group_id', 'greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_time', 'approval_id'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_time', 'approval_id', 'jenis'], 'integer'],
            [['note', 'reject_note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['no_referensi', 'no_document', 'vendor', 'color'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'approval_id' => 'Approval ID',
            'approval_time' => 'Approval Time',
            'reject_note' => 'Reject Note',
            'color' => 'Color',
            'jenis' => 'Jenis',
            'no_referensi' => 'No. Referensi',
            'jenisName' => 'Jenis',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfpItems()
    {
        return $this->hasMany(TrnBuyPfpItem::className(), ['buy_pfp_id' => 'id']);
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
}
