<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mst_mesin_processing_forbidden_greige".
 *
 * @property int $id
 * @property int $mesin_id
 * @property int $greige_id
 *
 * @property MstMesinProcessing $mesin
 * @property MstGreige $greige
 */
class MstMesinProcessingForbiddenGreige extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_mesin_processing_forbidden_greige';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mesin_id', 'greige_id'], 'required'],
            [['mesin_id', 'greige_id'], 'default', 'value' => null],
            [['mesin_id', 'greige_id'], 'integer'],
            [['mesin_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstMesinProcessing::className(), 'targetAttribute' => ['mesin_id' => 'id']],
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
            'mesin_id' => 'Mesin ID',
            'greige_id' => 'Greige',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMesin()
    {
        return $this->hasOne(MstMesinProcessing::className(), ['id' => 'mesin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }
}
