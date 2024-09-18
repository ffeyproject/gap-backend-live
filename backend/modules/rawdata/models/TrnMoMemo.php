<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_mo_memo".
 *
 * @property int $id
 * @property int $mo_id
 * @property string $memo
 * @property int|null $created_at
 *
 * @property TrnMo $mo
 */
class TrnMoMemo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mo_memo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mo_id', 'memo'], 'required'],
            [['mo_id', 'created_at'], 'default', 'value' => null],
            [['mo_id', 'created_at'], 'integer'],
            [['memo'], 'string'],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mo_id' => 'Mo ID',
            'memo' => 'Memo',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }
}
