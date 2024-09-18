<?php

namespace backend\modules\rawdata\models;

use Yii;

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
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnBuyPfpItem[] $trnBuyPfpItems
 */
class TrnBuyPfp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_buy_pfp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'no_document', 'vendor', 'date', 'created_at', 'created_by'], 'required'],
            [['greige_group_id', 'greige_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'integer'],
            [['note', 'reject_note'], 'string'],
            [['date'], 'safe'],
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
     * Gets query for [[TrnBuyPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfpItems()
    {
        return $this->hasMany(TrnBuyPfpItem::className(), ['buy_pfp_id' => 'id']);
    }
}
