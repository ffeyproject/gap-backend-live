<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mst_process_dyeing_planning_option".
 *
 * @property int $id
 * @property int $process_id
 * @property int $slot
 * @property string|null $label
 * @property string|null $color
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property MstProcessDyeing $process
 * @property TrnKartuProsesDyeingPlanning[] $trnKartuProsesDyeingPlannings
 */
class MstProcessDyeingPlanningOption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_process_dyeing_planning_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['process_id', 'slot'], 'required'],
            [['process_id', 'slot', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['process_id', 'slot', 'created_at', 'updated_at'], 'integer'],
            [['label'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 10],
            [['process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstProcessDyeing::className(), 'targetAttribute' => ['process_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'process_id' => 'Process ID',
            'slot' => 'Slot',
            'label' => 'Label',
            'color' => 'Color',
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
     * Gets query for [[TrnKartuProsesDyeingPlannings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingPlannings()
    {
        return $this->hasMany(TrnKartuProsesDyeingPlanning::className(), ['option_id' => 'id']);
    }
}
