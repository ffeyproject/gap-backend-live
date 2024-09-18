<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_buy_greige_item".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $buy_greige_id
 * @property int $qty kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)
 * @property string|null $note
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnBuyGreige $buyGreige
 */
class TrnBuyGreigeItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_buy_greige_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'required'],
            [['greige_group_id', 'greige_id', 'buy_greige_id', 'qty'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'buy_greige_id'], 'integer'],
            ['qty', 'number'],
            [['note'], 'string'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['buy_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnBuyGreige::className(), 'targetAttribute' => ['buy_greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'buy_greige_id' => 'Buy Greige ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[GreigeGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * Gets query for [[BuyGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuyGreige()
    {
        return $this->hasOne(TrnBuyGreige::className(), ['id' => 'buy_greige_id']);
    }
}
