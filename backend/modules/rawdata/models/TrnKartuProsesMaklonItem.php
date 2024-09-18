<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_kartu_proses_maklon_item".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int $kartu_process_id
 * @property string|null $note
 * @property int $qty Bukan panjang dalam meter, melainkan panjang/berat dalam unit greige group
 *
 * @property TrnKartuProsesMaklon $kartuProcess
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 */
class TrnKartuProsesMaklonItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_maklon_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'qty'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'qty'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_id', 'qty'], 'integer'],
            [['note'], 'string'],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesMaklon::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
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
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'kartu_process_id' => 'Kartu Process ID',
            'note' => 'Note',
            'qty' => 'Qty',
        ];
    }

    /**
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesMaklon::className(), ['id' => 'kartu_process_id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
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
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }
}
