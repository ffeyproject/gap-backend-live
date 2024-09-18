<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_inspecting_item".
 *
 * @property int $id
 * @property string $no
 * @property string|null $note
 * @property int $qty
 * @property int $trn_inspecting_roll_id
 *
 * @property TrnInspectingRoll $trnInspectingRoll
 */
class TrnInspectingItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_inspecting_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no', 'qty', 'trn_inspecting_roll_id'], 'required'],
            [['note'], 'string'],
            [['qty', 'trn_inspecting_roll_id'], 'default', 'value' => null],
            [['qty', 'trn_inspecting_roll_id'], 'integer'],
            [['no'], 'string', 'max' => 255],
            [['trn_inspecting_roll_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnInspectingRoll::className(), 'targetAttribute' => ['trn_inspecting_roll_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no' => 'No',
            'note' => 'Note',
            'qty' => 'Qty',
            'trn_inspecting_roll_id' => 'Trn Inspecting Roll ID',
        ];
    }

    /**
     * Gets query for [[TrnInspectingRoll]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectingRoll()
    {
        return $this->hasOne(TrnInspectingRoll::className(), ['id' => 'trn_inspecting_roll_id']);
    }
}
