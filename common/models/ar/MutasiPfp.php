<?php

namespace common\models\ar;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "mutasi_pfp".
 *
 * @property int $id
 * @property int|null $greige_group_id
 * @property int $greige_id
 * @property string $no_wo
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
 * @property int $status
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MutasiPfpItem[] $mutasiPfpItems
 * 
 * @property string $statusName
 * @property string $greigeName
 * @property string $greigeGroupName
 */
class MutasiPfp extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_DITERIMA = 3; const STATUS_DITOLAK = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_DITERIMA => 'Diterima', self::STATUS_DITOLAK => 'Ditolak'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_pfp';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => 'no_urut',
                ],
                'value' => function ($event) {
                    /* @var $model self */
                    $model = $event->sender;
                    if ($model->isNewRecord && empty($model->no_urut)) {
                        $max = \common\models\ar\MutasiPfp::find()->where(['date' => $model->date])->max('no_urut');
                        return $max === null ? 1 : $max + 1;
                    }
                    return $model->no_urut;
                },
            ],
            [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => 'no',
                ],
                'value' => function ($event) {
                    /* @var $model self */
                    $model = $event->sender;
                    if ($model->isNewRecord && empty($model->no)) {
                        // format: YearMonth/0000 -> 2606/0001
                        $date = date('ym', strtotime($model->date));
                        $noUrut = sprintf("%04d", $model->no_urut);
                        return $date . '/' . $noUrut;
                    }
                    return $model->no;
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time', 'status'], 'integer'],
            [['greige_id', 'no_wo', 'date'], 'required'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
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
     * Gets query for [[MutasiPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiPfpItems()
    {
        return $this->hasMany(MutasiPfpItem::className(), ['mutasi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'approval_id']);
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

        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(year FROM "date") = '.$y),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
