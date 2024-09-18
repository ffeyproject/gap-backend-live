<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_customer".
 *
 * @property int $id
 * @property string $cust_no
 * @property string $name
 * @property string $telp
 * @property string|null $fax
 * @property string|null $email
 * @property string|null $address
 * @property string|null $cp_name
 * @property string|null $cp_phone
 * @property string|null $cp_email
 * @property string|null $npwp
 * @property bool|null $aktif
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
            [['cust_no', 'name', 'telp'], 'required'],
            [['address'], 'string'],
            [['aktif'], 'boolean'],
            [['cust_no', 'name', 'email', 'cp_name', 'cp_email', 'npwp'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'email' => 'Email',
            'address' => 'Address',
            'cp_name' => 'Cp Name',
            'cp_phone' => 'Cp Phone',
            'cp_email' => 'Cp Email',
            'npwp' => 'Npwp',
            'aktif' => 'Aktif',
        ];
    }

    /**
     * Gets query for [[TrnScs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs()
    {
        return $this->hasMany(TrnSc::className(), ['cust_id' => 'id']);
    }
}
