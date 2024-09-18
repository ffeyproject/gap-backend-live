<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "kartu_process_pfp_process".
 *
 * @property int $kartu_process_id
 * @property int $process_id
 * @property string $value
 * @property string|null $note
 *
 * @property MstProcessPfp $process
 * @property TrnKartuProsesPfp $kartuProcess
 */
class KartuProcessPfpProcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kartu_process_pfp_process';
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
            [['process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstProcessPfp::className(), 'targetAttribute' => ['process_id' => 'id']],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesPfp::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
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
        return $this->hasOne(MstProcessPfp::className(), ['id' => 'process_id']);
    }

    /**
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesPfp::className(), ['id' => 'kartu_process_id']);
    }
}
