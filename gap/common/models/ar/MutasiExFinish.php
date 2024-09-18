<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "mutasi_ex_finish".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property string|null $no_wo
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $note
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $approval_id
 * @property int|null $approval_time
 * @property string|null $reject_note
 * @property int $status 1=Draft, 2=Posted, 3=Approved
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MutasiExFinishItem[] $mutasiExFinishItems
 *
 * @property string $greigeName
 * @property string $greigeGroupName
 * @property string $statusName
 */
class MutasiExFinish extends \yii\db\ActiveRecord
{
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
        return 'mutasi_ex_finish';
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
            [['greige_id', 'date', 'no_wo'], 'required'],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'approval_time', 'approval_id'], 'integer'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['note', 'reject_note'], 'string'],
            [['no_wo', 'no'], 'string', 'max' => 255],
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
            'no_wo' => 'No Wo',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'note' => 'Note',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'approval_id' => 'Approval ID',
            'approval_time' => 'Approval Time',
            'reject_note' => 'Reject Note',
            'status' => 'Status',
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
    public function getMutasiExFinishItems()
    {
        return $this->hasMany(MutasiExFinishItem::className(), ['mutasi_id' => 'id']);
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

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $dateArr = explode('-', $this->date);
        $y = $dateArr[0];

        /* @var $lastData MutasiExFinish*/
        $lastData = MutasiExFinish::find()
            ->where([
                'and',
                ['not', ['mutasi_ex_finish.no_urut' => null]],
                new Expression('EXTRACT(year FROM "mutasi_ex_finish"."date") = '.$y),
            ])
            ->orderBy(['mutasi_ex_finish.no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
