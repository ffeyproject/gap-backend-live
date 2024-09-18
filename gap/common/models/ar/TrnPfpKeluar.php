<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_pfp_keluar".
 *
 * @property int $id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $jenis 1=Pergantian, 2=Makloon, 3=Jual, 4=Sample, 5=lain-lain
 * @property string|null $destinasi nama orang/divisi/instansi yang mengambil barang
 * @property string|null $no_referensi
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
 * @property TrnPfpKeluarItem[] $trnPfpKeluarItems
 * @property TrnStockGreige[] $stockPfps
 * @property string $jenisName
 * @property string $approvalName
 */
class TrnPfpKeluar extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;const STATUS_REJECTED = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved', self::STATUS_REJECTED => 'Rejected'];
    }

    const JENIS_PERGANTIAN = 1;const JENIS_MAKLOON = 2;const JENIS_JUAL = 3; const JENIS_SAMPLE = 4; const JENIS_LAIN_LAIN = 5;
    /**
     * @return array
     */
    public static function jenisOptions(){
        return [self::JENIS_PERGANTIAN => 'Pergantian', self::JENIS_MAKLOON => 'Makloon', self::JENIS_JUAL => 'Jual', self::JENIS_SAMPLE => 'Sample', self::JENIS_LAIN_LAIN => 'Lain-lain'];
    }

    /**
     * @return string
    */
    public function getJenisName(){
        return self::jenisOptions()[$this->jenis];
    }

    /**
     * @return string|null
     */
    public function getApprovalName(){
        $q = (new \yii\db\Query())
            ->select(['username'])
            ->from(\common\models\User::tableName())
            ->where(['id' => $this->approved_by])
            ->one();

        if($q !== null){
            return $q['username'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_pfp_keluar';
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
            [['date', 'jenis'], 'required'],

            [['no_urut', 'posted_at', 'approved_at', 'approved_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['no_urut', 'jenis', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED, self::STATUS_REJECTED]],

            ['jenis', 'in', 'range' => [self::JENIS_PERGANTIAN, self::JENIS_MAKLOON, self::JENIS_JUAL, self::JENIS_SAMPLE, self::JENIS_LAIN_LAIN]],

            [['date'], 'date', 'format'=>'php:Y-m-d'],
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
            'jenis' => 'Jenis',
            'destinasi' => 'Destinasi',
            'no_referensi' => 'No Referensi',
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
            'jenisName' => 'Jenis',
            'approvalName' => 'Diperintahkan Oleh',
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
     * Gets query for [[TrnPfpKeluarItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnPfpKeluarItems()
    {
        return $this->hasMany(TrnPfpKeluarItem::className(), ['pfp_keluar_id' => 'id']);
    }

    /**
     * Gets query for [[StockPfps]].
     *
     * @return yii\db\ActiveQuery
     * @throws yii\base\InvalidConfigException
     */
    public function getStockPfps()
    {
        return $this->hasMany(TrnStockGreige::className(), ['id' => 'stock_pfp_id'])->viaTable('trn_pfp_keluar_item', ['pfp_keluar_id' => 'id']);
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
            case $this::JENIS_PERGANTIAN:
                $jenisLabel = 'PP';
                break;
            case $this::JENIS_MAKLOON:
                $jenisLabel = 'MP';
                break;
            case $this::JENIS_JUAL:
                $jenisLabel = 'JP';
                break;
            case $this::JENIS_SAMPLE:
                $jenisLabel = 'SP';
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
