<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_retur_buyer_item".
 *
 * @property int $id
 * @property int $retur_buyer_id
 * @property int $qty
 * @property string|null $note
 * @property int $grade mereferensi ke TrnStockGreige::gradeOptions()
 *
 * @property TrnReturBuyer $returBuyer
 */
class TrnReturBuyerItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_retur_buyer_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'grade'], 'required'],
            [['retur_buyer_id', 'qty'], 'default', 'value' => null],
            [['retur_buyer_id', 'qty'], 'integer'],
            [['note'], 'string'],

            ['grade', 'default', 'value'=>TrnStockGreige::GRADE_A],
            ['grade', 'in', 'range' => [TrnStockGreige::GRADE_A, TrnStockGreige::GRADE_B, TrnStockGreige::GRADE_C, TrnStockGreige::GRADE_D, TrnStockGreige::GRADE_E, TrnStockGreige::GRADE_NG]],

            [['retur_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnReturBuyer::className(), 'targetAttribute' => ['retur_buyer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'retur_buyer_id' => 'Retur Buyer ID',
            'qty' => 'Qty',
            'note' => 'Note',
            'grade' => 'Grade',
        ];
    }

    /**
     * Gets query for [[ReturBuyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturBuyer()
    {
        return $this->hasOne(TrnReturBuyer::className(), ['id' => 'retur_buyer_id']);
    }
}
