<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_kirim_buyer_bal".
 *
 * @property int $id
 * @property int $trn_kirim_buyer_id
 * @property string $no_bal
 * @property int $header_id
 *
 * @property TrnKirimBuyer $trnKirimBuyer
 */
class TrnKirimBuyerBal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_buyer_bal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_bal', 'header_id'], 'required'],
            [['trn_kirim_buyer_id'], 'default', 'value' => null],
            [['trn_kirim_buyer_id', 'header_id'], 'integer'],
            [['no_bal'], 'string', 'max' => 45],
            [['no_bal', 'header_id'], 'unique', 'targetAttribute' => ['no_bal', 'header_id']],
            [['trn_kirim_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimBuyer::className(), 'targetAttribute' => ['trn_kirim_buyer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trn_kirim_buyer_id' => 'Trn Kirim Buyer ID',
            'no_bal' => 'No Bal',
            'header_id' => 'Header ID',
        ];
    }

    /**
     * Gets query for [[TrnKirimBuyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKirimBuyer()
    {
        return $this->hasOne(TrnKirimBuyer::className(), ['id' => 'trn_kirim_buyer_id']);
    }
}