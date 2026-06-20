<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_kartu_proses_dyeing_planning".
 *
 * @property int $kartu_process_id
 * @property int $process_id
 * @property bool|null $is_siap
 * @property int|null $option_id
 * @property string|null $catatan
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property MstProcessDyeing $process
 * @property MstProcessDyeingPlanningOption $option
 * @property TrnKartuProsesDyeing $kartuProcess
 */
class TrnKartuProsesDyeingPlanning extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_dyeing_planning';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'process_id'], 'required'],
            [['kartu_process_id', 'process_id', 'option_id', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['kartu_process_id', 'process_id', 'option_id', 'created_at', 'updated_at'], 'integer'],
            [['is_siap'], 'boolean'],
            [['catatan'], 'string'],
            [['kartu_process_id', 'process_id'], 'unique', 'targetAttribute' => ['kartu_process_id', 'process_id']],
            [['process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstProcessDyeing::className(), 'targetAttribute' => ['process_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstProcessDyeingPlanningOption::className(), 'targetAttribute' => ['option_id' => 'id']],
            [['kartu_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesDyeing::className(), 'targetAttribute' => ['kartu_process_id' => 'id']],
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
            'is_siap' => 'Is Siap',
            'option_id' => 'Option ID',
            'catatan' => 'Catatan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[Option]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(MstProcessDyeingPlanningOption::className(), ['id' => 'option_id']);
    }

    /**
     * Gets query for [[KartuProcess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcess()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_id']);
    }
}
