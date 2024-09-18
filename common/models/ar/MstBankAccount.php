<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_bank_account".
 *
 * @property int $id
 * @property string $bank_name
 * @property string $acct_no
 * @property string $acct_name
 * @property string $swift_code
 * @property string $address
 * @property string $correspondence
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
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

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs()
    {
        return $this->hasMany(TrnSc::className(), ['bank_acct_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function optionList(){
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'id', 'bank_name');
    }
}
