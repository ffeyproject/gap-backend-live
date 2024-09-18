<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_vendor".
 *
 * @property int $id
 * @property string $name
 * @property string $telp
 * @property string|null $fax
 * @property string|null $email
 * @property string|null $address
 * @property string|null $cp_name
 * @property bool|null $aktif
 *
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 */
class MstVendor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'telp'], 'required'],
            [['address'], 'string'],
            [['aktif'], 'boolean'],
            [['name', 'email', 'cp_name'], 'string', 'max' => 255],
            [['telp', 'fax'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'email' => 'Email',
            'address' => 'Address',
            'cp_name' => 'Cp Name',
            'aktif' => 'Aktif',
        ];
    }

    /**
     * Gets query for [[TrnKartuProsesMaklons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['vendor_id' => 'id']);
    }
}
