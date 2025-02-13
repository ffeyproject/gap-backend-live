<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_kirim_makloon_v2_item".
 *
 * @property int $id
 * @property int $kirim_makloon_id
 * @property int $qty
 * @property string|null $note
 *
 * @property TrnKirimMakloonV2 $kirimMakloon
 */
class TrnKirimMakloonV2Item extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_makloon_v2_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            [['kirim_makloon_id', 'qty'], 'default', 'value' => null],
            [['kirim_makloon_id', 'qty'], 'integer'],
            [['note'], 'string'],
            [['kirim_makloon_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimMakloonV2::className(), 'targetAttribute' => ['kirim_makloon_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kirim_makloon_id' => 'Kirim Makloon ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[KirimMakloon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKirimMakloon()
    {
        return $this->hasOne(TrnKirimMakloonV2::className(), ['id' => 'kirim_makloon_id']);
    }
}
