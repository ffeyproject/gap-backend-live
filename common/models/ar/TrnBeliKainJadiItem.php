<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_beli_kain_jadi_item".
 *
 * @property int $id
 * @property int $beli_kain_jadi_id
 * @property float $qty
 * @property string|null $note
 *
 * @property TrnBeliKainJadi $beliKainJadi
 */
class TrnBeliKainJadiItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_beli_kain_jadi_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            [['beli_kain_jadi_id', 'qty'], 'default', 'value' => null],
            [['beli_kain_jadi_id'], 'integer'],
            ['qty', 'number'],
            [['note'], 'string'],
            [['beli_kain_jadi_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnBeliKainJadi::className(), 'targetAttribute' => ['beli_kain_jadi_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'beli_kain_jadi_id' => 'Beli Kain Jadi ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[BeliKainJadi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBeliKainJadi()
    {
        return $this->hasOne(TrnBeliKainJadi::className(), ['id' => 'beli_kain_jadi_id']);
    }
}
