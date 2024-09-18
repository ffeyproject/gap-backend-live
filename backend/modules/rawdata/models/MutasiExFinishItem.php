<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mutasi_ex_finish_item".
 *
 * @property int $id
 * @property int $mutasi_id
 * @property int $panjang_m
 * @property string|null $note
 * @property int $greige_id
 * @property int $greige_group_id
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property MutasiExFinish $mutasi
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
            [['mutasi_id', 'panjang_m', 'greige_id', 'greige_group_id'], 'required'],
            [['mutasi_id', 'panjang_m', 'greige_id', 'greige_group_id'], 'default', 'value' => null],
            [['mutasi_id', 'panjang_m', 'greige_id', 'greige_group_id'], 'integer'],
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
            'mutasi_id' => 'Mutasi ID',
            'panjang_m' => 'Panjang M',
            'note' => 'Note',
            'greige_id' => 'Greige ID',
            'greige_group_id' => 'Greige Group ID',
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
     * Gets query for [[Mutasi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasi()
    {
        return $this->hasOne(MutasiExFinish::className(), ['id' => 'mutasi_id']);
    }
}
