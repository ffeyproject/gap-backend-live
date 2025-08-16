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

    public static function generateCustNo($name)
{
    // daftar prefix yang harus diabaikan
    $ignore = ['PT', 'PT.', 'CV', 'CV.', 'UD', 'UD.', 'PD', 'PD.', 'Pt.', 'Pt.', 'Cv.', 'Cv.', 'Ud.', 'Ud.', 'Pd.', 'Pd.', 'KOP', 'KOP.'];

    // pecah nama jadi array
    $parts = preg_split('/\s+/', strtoupper($name));

    // cek jika kata pertama ada di daftar ignore
    if (in_array($parts[0], $ignore)) {
        array_shift($parts); // buang kata pertama
    }

    // ambil 3 huruf pertama dari nama buyer setelah ignore
    $prefix = strtoupper(substr($parts[0], 0, 3));

    // cari cust_no terakhir di database dengan prefix ini
    $last = self::find()
        ->where(['like', 'cust_no', $prefix])
        ->orderBy(['cust_no' => SORT_DESC])
        ->one();

    if ($last) {
        // ambil angka di belakang prefix, default 0
        $lastNumber = (int) substr($last->cust_no, 3);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    // format nomor 3 digit
    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

}