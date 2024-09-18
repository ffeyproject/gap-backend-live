<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_greige_keluar".
 *
 * @property int $id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $note
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by yang memerintahkan pengeluaran greige jika ada
 * @property int $status 1=draft, 2=posted, 3=approved, 4=rejected
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property int $jenis 1=sample, 2=jual, 3=makloon, 4=lain-lain
 * @property string $destinasi nama orang/divisi/instansi yang mengambil barang
 * @property string $no_referensi
 *
 * @property TrnGreigeKeluarItem[] $trnGreigeKeluarItems
 * @property TrnStockGreige[] $stockGreiges
 */
class TrnGreigeKeluar extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;const STATUS_REJECTED = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved', self::STATUS_REJECTED => 'Rejected'];
    }

    const JENIS_SAMPLE = 1;const JENIS_JUAL = 2;const JENIS_MAKLOON = 3;const JENIS_LAIN_LAIN = 4;
    /**
     * @return array
     */
    public static function jenisOptions(){
        return [self::JENIS_SAMPLE => 'Sample', self::JENIS_JUAL => 'Jual', self::JENIS_MAKLOON => 'Makloon', self::JENIS_LAIN_LAIN => 'Lain-lain'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_greige_keluar';
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
            [['no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['date', 'jenis'], 'required'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED, self::STATUS_REJECTED]],

            ['jenis', 'default', 'value'=>self::JENIS_SAMPLE],
            ['jenis', 'in', 'range' => [self::JENIS_SAMPLE, self::JENIS_JUAL, self::JENIS_MAKLOON, self::JENIS_LAIN_LAIN]],

            [['note'], 'string'],
            [['no', 'destinasi', 'no_referensi'], 'string', 'max' => 255],
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
            'date' => 'Date',
            'note' => 'Note',
            'posted_at' => 'Posted At',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'jenis' => 'Jenis',
            'destinasi' => 'Destinasi',
            'no_referensi' => 'Nomor Referensi'
        ];
    }

    /**
     * @inheritDoc
    */
    public function attributeHints()
    {
        return [
            'approved_by' => 'Yang memerintahkan pengambilan greige, boleh dikosongkan.',
            'note' => 'Tulis catatan disini',
        ];
    }

    /**
     * Gets query for [[TrnGreigeKeluarItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnGreigeKeluarItems()
    {
        return $this->hasMany(TrnGreigeKeluarItem::className(), ['greige_keluar_id' => 'id']);
    }

    /**
     * Gets query for [[StockGreiges]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getStockGreiges()
    {
        return $this->hasMany(TrnStockGreige::className(), ['id' => 'stock_greige_id'])->viaTable('trn_greige_keluar_item', ['greige_keluar_id' => 'id']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        /*21/01/S00001 (sample)
        21/01/J00001 (jual)
        21/01/M00001 (makloon)
        21/01/LL00001 (lain-lain)*/

        switch ($this->jenis){
            case $this::JENIS_SAMPLE:
                $jenisLabel = 'S';
                break;
            case $this::JENIS_JUAL:
                $jenisLabel = 'J';
                break;
            case $this::JENIS_MAKLOON:
                $jenisLabel = 'M';
                break;
            case $this::JENIS_LAIN_LAIN:
                $jenisLabel = 'LL';
                break;
            default:
                $jenisLabel = '';
        }

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/{$jenisLabel}{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesMaklon*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                ['jenis' => $this->jenis],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->asArray()
            ->one();

        if($lastData !== null){
            $this->no_urut = (int)$lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
