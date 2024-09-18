<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "jual_ex_finish_item".
 *
 * @property int $id
 * @property int $jual_id
 * @property int $no_wo
 * @property int $greige_id
 * @property int $grade Mengacu pada TrnStockGreige::gradeOptions()
 * @property float $qty
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 *
 * @property JualExFinish $jual
 * @property MstGreige $greige
 */
class JualExFinishItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jual_ex_finish_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jual_id', 'greige_id', 'grade', 'qty', 'unit'], 'required'],
            [['jual_id', 'greige_id', 'grade', 'unit'], 'default', 'value' => null],
            [['jual_id', 'greige_id', 'grade', 'unit'], 'integer'],
            [['qty'], 'number'],
            ['no_wo', 'string'],
            [['jual_id'], 'exist', 'skipOnError' => true, 'targetClass' => JualExFinish::className(), 'targetAttribute' => ['jual_id' => 'id']],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jual_id' => 'Jual ID',
            'no_wo' => 'No WO',
            'greige_id' => 'Greige ID',
            'grade' => 'Grade',
            'qty' => 'Qty',
            'unit' => 'Unit',
        ];
    }

    /**
     * Gets query for [[Jual]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJual()
    {
        return $this->hasOne(JualExFinish::className(), ['id' => 'jual_id']);
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
}
