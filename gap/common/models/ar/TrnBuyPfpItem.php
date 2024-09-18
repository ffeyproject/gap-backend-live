<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_buy_pfp_item".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $buy_pfp_id
 * @property int $panjang_m
 * @property string|null $note
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnBuyPfp $buyPfp
 */
class TrnBuyPfpItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_buy_pfp_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panjang_m'], 'required'],
            [['greige_group_id', 'greige_id', 'buy_pfp_id', 'panjang_m'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'buy_pfp_id', 'panjang_m'], 'integer'],
            [['note'], 'string'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['buy_pfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnBuyPfp::className(), 'targetAttribute' => ['buy_pfp_id' => 'id']],
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
            'buy_pfp_id' => 'Buy Pfp ID',
            'panjang_m' => 'Panjang M',
            'note' => 'Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuyPfp()
    {
        return $this->hasOne(TrnBuyPfp::className(), ['id' => 'buy_pfp_id']);
    }
}
