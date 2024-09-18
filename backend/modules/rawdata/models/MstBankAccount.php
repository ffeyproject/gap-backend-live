<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_bank_account".
 *
 * @property int $id
 * @property string $bank_name
 * @property string $acct_no
 * @property string $acct_name
 * @property string|null $swift_code
 * @property string|null $address
 * @property string|null $correspondence
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnSc[] $trnScs
 */
class MstBankAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_bank_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_name', 'acct_no', 'acct_name'], 'required'],
            [['address', 'correspondence'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['bank_name', 'acct_no', 'acct_name', 'swift_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bank_name' => 'Bank Name',
            'acct_no' => 'Acct No',
            'acct_name' => 'Acct Name',
            'swift_code' => 'Swift Code',
            'address' => 'Address',
            'correspondence' => 'Correspondence',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TrnScs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs()
    {
        return $this->hasMany(TrnSc::className(), ['bank_acct_id' => 'id']);
    }
}
