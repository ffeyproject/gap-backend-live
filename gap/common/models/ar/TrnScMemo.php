<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_sc_memo".
 *
 * @property int $id
 * @property int $sc_id
 * @property string $memo
 * @property int|null $created_at
 *
 * @property TrnSc $sc
 */
class TrnScMemo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_memo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'memo'], 'required'],
            [['sc_id', 'created_at'], 'default', 'value' => null],
            [['sc_id', 'created_at'], 'integer'],
            [['memo'], 'string'],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
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
            'memo' => 'Memo',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }
}
