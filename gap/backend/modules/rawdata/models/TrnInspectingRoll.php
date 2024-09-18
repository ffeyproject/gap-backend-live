<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_inspecting_roll".
 *
 * @property int $id
 * @property int $inspecting_id
 * @property string|null $no
 * @property int $grade 1=Grade A, 2=Grade B, 3=Grade C, 4=Piece Kecil, 5=Sample
 *
 * @property TrnInspectingItem[] $trnInspectingItems
 * @property TrnInspecting $inspecting
 */
class TrnInspectingRoll extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_inspecting_roll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspecting_id', 'grade'], 'required'],
            [['inspecting_id', 'grade'], 'default', 'value' => null],
            [['inspecting_id', 'grade'], 'integer'],
            [['no'], 'string', 'max' => 255],
            [['inspecting_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnInspecting::className(), 'targetAttribute' => ['inspecting_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inspecting_id' => 'Inspecting ID',
            'no' => 'No',
            'grade' => 'Grade',
        ];
    }

    /**
     * Gets query for [[TrnInspectingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectingItems()
    {
        return $this->hasMany(TrnInspectingItem::className(), ['trn_inspecting_roll_id' => 'id']);
    }

    /**
     * Gets query for [[Inspecting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspecting()
    {
        return $this->hasOne(TrnInspecting::className(), ['id' => 'inspecting_id']);
    }
}
