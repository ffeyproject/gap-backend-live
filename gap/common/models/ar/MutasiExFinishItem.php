<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mutasi_ex_finish_item".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $mutasi_id
 * @property int $panjang_m
 * @property string|null $note
 * @property int $grade mengacu kepada TrnStockGreige::gradeOptions()
 *
 * @property MutasiExFinish $mutasi
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 */
class MutasiExFinishItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_ex_finish_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panjang_m'], 'required'],
            [['mutasi_id', 'panjang_m'], 'default', 'value' => null],
            ['grade', 'default', 'value'=>TrnStockGreige::GRADE_NG],
            ['grade', 'in', 'range' => [TrnStockGreige::GRADE_A, TrnStockGreige::GRADE_B, TrnStockGreige::GRADE_C, TrnStockGreige::GRADE_D, TrnStockGreige::GRADE_E, TrnStockGreige::GRADE_NG]],
            [['mutasi_id'], 'integer'],
            ['panjang_m', 'number'],
            [['note'], 'string'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['mutasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasiExFinish::className(), 'targetAttribute' => ['mutasi_id' => 'id']],
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
            'mutasi_id' => 'Mutasi ID',
            'panjang_m' => 'Panjang M',
            'note' => 'Note',
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
    public function getMutasi()
    {
        return $this->hasOne(MutasiExFinish::className(), ['id' => 'mutasi_id']);
    }
}
