<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_kirim_buyer_header".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $date
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $status 1=Draft, 2=Posted
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $pengirim
 * @property string|null $penerima
 * @property string|null $kepala_gudang
 * @property string|null $note
 * @property string|null $nama_buyer
 * @property string|null $alamat_buyer
 * @property string|null $plat_nomor
 * @property bool $is_export
 * @property bool $is_resmi Surat jalan nya resmi atau tidak
 *
 * @property TrnKirimBuyer[] $trnKirimBuyers
 * @property MstCustomer $customer
 */
class TrnKirimBuyerHeader extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_buyer_header';
    }

    /**
     * {@inheritdoc}
     */
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
            [['customer_id', 'date', 'pengirim', 'penerima', 'kepala_gudang', 'nama_buyer', 'alamat_buyer'], 'required'],
            [['customer_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'default', 'value' => null],
            [['customer_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['is_export', 'is_resmi'], 'boolean'],
            ['is_export', 'default', 'value'=>false],
            ['is_resmi', 'default', 'value'=>true],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED]],

            [['pengirim', 'penerima', 'kepala_gudang', 'nama_buyer', 'alamat_buyer', 'plat_nomor'], 'string', 'max' => 255],
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
            'customer_id' => 'Customer ID',
            'date' => 'Date',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'pengirim' => 'Pengirim',
            'penerima' => 'Penerima',
            'kepala_gudang' => 'Kepala Gudang',
            'note' => 'Note',
            'nama_buyer' => 'Nama Buyer Di Surat Jalan',
            'alamat_buyer' => 'Alamat Buyer Di Surat Jalan',
            'is_export' => 'Export',
            'is_resmi' => 'Resmi'
        ];
    }

    /**
     * Gets query for [[TrnKirimBuyers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKirimBuyers()
    {
        return $this->hasMany(TrnKirimBuyer::className(), ['header_id' => 'id']);
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

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKirimBuyer*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
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
