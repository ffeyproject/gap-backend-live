<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_order_celup".
 *
 * @property int $id
 * @property int $sc_id
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
 * @property string|null $color
 *
 * @property TrnKartuProsesCelup[] $trnKartuProsesCelups
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItems
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MstHandling $handling
 * @property TrnSc $sc
 * @property User $createdBy
 * @property User $updatedBy
 */
class TrnOrderCelup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_order_celup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'greige_group_id', 'greige_id', 'qty', 'date', 'created_at', 'created_by', 'handling_id'], 'required'],
            [['sc_id', 'greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id'], 'default', 'value' => null],
            [['sc_id', 'greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id'], 'integer'],
            [['qty'], 'number'],
            [['note'], 'string'],
            [['date'], 'safe'],
            [['no', 'color'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
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
            'color' => 'Color',
        ];
    }

    /**
     * Gets query for [[TrnKartuProsesCelups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelups()
    {
        return $this->hasMany(TrnKartuProsesCelup::className(), ['order_celup_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesCelupItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelupItems()
    {
        return $this->hasMany(TrnKartuProsesCelupItem::className(), ['order_celup_id' => 'id']);
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
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
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
