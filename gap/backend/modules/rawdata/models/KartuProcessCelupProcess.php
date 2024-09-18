<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "kartu_process_celup_process".
 *
 * @property int $kartu_process_id
 * @property int $process_id
 * @property string $value
 * @property string|null $note
 *
 * @property MstProcessDyeing $process
 * @property TrnKartuProsesCelup $kartuProcess
 */
class KartuProcessCelupProcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kartu_process_celup_process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'process_id', 'value'], 'required'],
            [['kartu_process_id', 'process_id'], 'default', 'value' => null],
            [['kartu_process_id', 'process_id'], 'integer'],
            [['value', 'note'], 'string'],
            [['kartu_process_id', 'process_id'], 'unique', 'targetAttribute' => ['kartu_process_id', 'process_id']],
            [['process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstProcessDyeing::className(), 'targetAttribute' => ['process_id' => 'id']],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesCelup::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kartu_process_id' => 'Kartu Process ID',
            'process_id' => 'Process ID',
            'value' => 'Value',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Process]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcess()
    {
        return $this->hasOne(MstProcessDyeing::className(), ['id' => 'process_id']);
    }

    /**
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesCelup::className(), ['id' => 'kartu_process_id']);
    }
}
