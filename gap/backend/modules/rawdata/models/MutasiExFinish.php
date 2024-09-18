<?php

namespace backend\modules\rawdata\models;

use Yii;

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
 * @property int $status 1=Draft, 2=Posted, 3=Approved
 * @property int|null $approval_id
 * @property int|null $approval_time
 * @property int|null $reject_note
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MutasiExFinishItem[] $mutasiExFinishItems
 */
class MutasiExFinish extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_ex_finish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'date', 'created_at', 'created_by'], 'required'],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'approval_id', 'approval_time', 'reject_note'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'approval_id', 'approval_time', 'reject_note'], 'integer'],
            [['date'], 'safe'],
            [['note'], 'string'],
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
            'status' => 'Status',
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
     * Gets query for [[MutasiExFinishItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishItems()
    {
        return $this->hasMany(MutasiExFinishItem::className(), ['mutasi_id' => 'id']);
    }
}
