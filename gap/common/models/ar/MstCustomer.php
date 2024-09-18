<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mst_customer".
 *
 * @property int $id
 * @property string $cust_no
 * @property string $telp
 * @property string $fax
 * @property string $email
 * @property string $address
 * @property string $cp_name
 * @property string $cp_phone
 * @property string $cp_email
 * @property string $npwp
 * @property bool $aktif
 * @property string $name
 *
 * @property TrnSc[] $trnScs
 */
class MstCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cust_no', 'telp', 'name'], 'required'],
            [['address'], 'string'],
            [['aktif'], 'boolean'],
            [['cust_no', 'email', 'cp_name', 'cp_email', 'npwp', 'name'], 'string', 'max' => 255],
            [['telp', 'fax', 'cp_phone'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cust_no' => 'Cust No',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'email' => 'Email',
            'address' => 'Address',
            'cp_name' => 'Cp Name',
            'cp_phone' => 'Cp Phone',
            'cp_email' => 'Cp Email',
            'npwp' => 'Npwp',
            'aktif' => 'Aktif',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs()
    {
        return $this->hasMany(TrnSc::className(), ['cust_id' => 'id']);
    }
}
