<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_wo_memo".
 *
 * @property int $id
 * @property int $wo_id
 * @property int $tahun
 * @property string $memo
 * @property int $no_urut
 * @property string $no
 * @property int|null $created_at
 *
 * @property TrnWo $wo
 */
class TrnWoMemo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_wo_memo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wo_id', 'memo'], 'required'],
            [['wo_id', 'no_urut', 'created_at'], 'default', 'value' => null],
            [['wo_id', 'created_at'], 'integer'],
            [['no', 'memo'], 'string'],
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
            'wo_id' => 'Wo ID',
            'memo' => 'Memo',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){
                $this->tahun = date('Y');
            }
            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }
}
