<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "jual_ex_finish".
 *
 * @property int $id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $jenis_gudang 1=Ex Retur Buyer, 2=Ex Gudang Jadi
 * @property int $customer_id
 * @property int $grade Mengacu pada TrnStockGreige::gradeOptions()
 * @property float $harga
 * @property string $no_po
 * @property int $ongkir Yang dibebani ongkir. Mengacu pada TrnSc::ongkosAngkutOptions()
 * @property string $pembayaran Metode pembayaran
 * @property string $tanggal_pengiriman
 * @property string $komisi
 * @property int $jenis_order Mengacu pada TrnScGreige::processOptions()
 * @property bool $is_resmi
 * @property string|null $keterangan
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstCustomer $customer
 * @property string $customerName
 * @property string $jenisGudangName
 * @property string $gradeName
 * @property string $ongkirName
 * @property string $jenisOrderName
 *
 * @property JualExFinishItem[] $jualExFinishItems
 * @property SuratJalanExFinish $pengiriman
 */
class JualExFinish extends \yii\db\ActiveRecord
{
    const JENIS_GUDANG_EX_RETUR = 1; const JENIS_GUDANG_EX_GD_JADI = 2;
    /**
     * @return array
     */
    public static function jenisGudangOptions(){
        return [
            self::JENIS_GUDANG_EX_RETUR => 'Ex Retur Buyer',
            self::JENIS_GUDANG_EX_GD_JADI => 'Ex Gudang Jadi',
        ];
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
    public static function tableName()
    {
        return 'jual_ex_finish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_gudang', 'customer_id', 'grade', 'harga', 'no_po', 'ongkir', 'pembayaran', 'tanggal_pengiriman', 'komisi', 'jenis_order', 'is_resmi'], 'required'],
            [['no_urut', 'jenis_gudang', 'customer_id', 'grade', 'ongkir', 'jenis_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['no_urut', 'jenis_gudang', 'customer_id', 'grade', 'ongkir', 'jenis_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['is_resmi', 'boolean'],
            ['is_resmi', 'default', 'value' => false],
            [['harga'], 'number'],
            [['tanggal_pengiriman'], 'date', 'format'=>'php:Y-m-d'],
            [['keterangan'], 'string'],
            [['no', 'no_po', 'pembayaran', 'komisi'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'jenis_gudang' => 'Jenis Gudang',
            'customer_id' => 'Customer ID',
            'grade' => 'Grade',
            'harga' => 'Harga',
            'no_po' => 'NO PO',
            'ongkir' => 'Ongkir',
            'pembayaran' => 'Pembayaran',
            'tanggal_pengiriman' => 'Tanggal Pengiriman',
            'komisi' => 'Komisi',
            'jenis_order' => 'Jenis Order',
            'is_resmi' => 'Is Resmi',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'customerName' => 'Customer',
            'jenisGudangName' => 'Jenis Gudang',
            'gradeName' => 'Grade',
            'ongkirName' => 'Ongkir',
            'jenisOrderName' => 'Jenis Order'
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(MstCustomer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer->name;
    }

    /**
     * @return string
     */
    public function getJenisGudangName()
    {
        return self::jenisGudangOptions()[$this->jenis_gudang];
    }

    /**
     * @return string
     */
    public function getGradeName()
    {
        return TrnStockGreige::gradeOptions()[$this->grade];
    }

    /**
     * @return string
     */
    public function getOngkirName()
    {
        return TrnSc::ongkosAngkutOptions()[$this->ongkir];
    }

    /**
     * @return string
     */
    public function getJenisOrderName()
    {
        return TrnScGreige::processOptions()[$this->jenis_order];
    }

    /**
     * Gets query for [[JualExFinishItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJualExFinishItems()
    {
        return $this->hasMany(JualExFinishItem::className(), ['jual_id' => 'id']);
    }

    /**
     * Gets query for [[Memo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPengiriman()
    {
        return $this->hasOne(SuratJalanExFinish::className(), ['memo_id' => 'id']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->tanggal_pengiriman);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);

        //resmi : S.21(thn).406(noUrut)
        //Tdk resmi : 406(noUrut)/S/21(thn)
        if($this->is_resmi){
            $this->no = 'S.'.$yearTwoDigit.'.'.$noUrut;
        }else{
            $this->no = $noUrut.'/S/'.$yearTwoDigit;
        }
    }

    private function setNoUrut(){
        $ts = strtotime($this->tanggal_pengiriman);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12

        /* @var $lastData JualExFinish*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                ['is_resmi' => $this->is_resmi],
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tanggal_pengiriman") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."tanggal_pengiriman") = \''.$y.'-'.$m.'\''),
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tanggal_pengiriman") = \''.$y.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
