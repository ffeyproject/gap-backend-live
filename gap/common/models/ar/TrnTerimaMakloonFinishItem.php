<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_terima_makloon_finish_item".
 *
 * @property int $id
 * @property int $terima_makloon_id
 * @property int $qty
 * @property string|null $note
 *
 * @property TrnTerimaMakloonFinish $terimaMakloon
 */
class TrnTerimaMakloonFinishItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_terima_makloon_finish_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            [['terima_makloon_id', 'qty'], 'default', 'value' => null],
            [['terima_makloon_id'], 'integer'],
            ['qty', 'number'],
            [['note'], 'string'],
            [['terima_makloon_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnTerimaMakloonFinish::className(), 'targetAttribute' => ['terima_makloon_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'terima_makloon_id' => 'Terima Makloon ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[TerimaMakloon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTerimaMakloon()
    {
        return $this->hasOne(TrnTerimaMakloonFinish::className(), ['id' => 'terima_makloon_id']);
    }
}
