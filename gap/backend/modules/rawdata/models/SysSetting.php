<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "sys_setting".
 *
 * @property int $id
 * @property string $nama_perusahaan
 * @property string $alamat
 * @property string $telp
 * @property string $fax
 * @property string $email
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class SysSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sys_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_perusahaan', 'alamat', 'telp', 'fax', 'email'], 'required'],
            [['alamat'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_perusahaan', 'email'], 'string', 'max' => 255],
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
            'nama_perusahaan' => 'Nama Perusahaan',
            'alamat' => 'Alamat',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'email' => 'Email',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
}
