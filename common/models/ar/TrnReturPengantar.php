<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_retur_pengantar".
 *
 * Surat pengantar stok hasil retur buyer (trn_gudang_jadi source Retur) dari gudang jadi ke bagian/divisi lain.
 * Saat posting:
 *  - bagian Processing: stok gudang jadi berstatus Mutasi Ke Processing dan dibuatkan stok Gudang Mutasi (trn_stock_greige)
 *    sehingga bisa dipilih di Kartu Proses Mutasi (dyeing / printing),
 *  - bagian lain: stok gudang jadi berstatus Out.
 *
 * @property int $id
 * @property string $date
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $bagian 1=Processing, 2=QC, 3=Marketing, 4=Lain-lain
 * @property string|null $bagian_lain
 * @property string|null $penerima
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted
 * @property int|null $posted_at
 * @property int|null $posted_by
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnReturPengantarItem[] $trnReturPengantarItems
 * @property string $bagianName
 */
class TrnReturPengantar extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted'];
    }

    const BAGIAN_PROCESSING = 1; const BAGIAN_QC = 2; const BAGIAN_MARKETING = 3; const Verpacking = 5; const BAGIAN_LAIN = 6;
    /**
     * @return array
     */
    public static function bagianOptions(){
        return [self::BAGIAN_PROCESSING => 'Processing', self::BAGIAN_QC => 'QC', self::BAGIAN_MARKETING => 'Marketing', self::Verpacking => 'Verpacking', self::BAGIAN_LAIN => 'Lain-lain'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_retur_pengantar';
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
            [['date', 'bagian'], 'required'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['bagian', 'in', 'range' => array_keys(self::bagianOptions())],
            ['bagian_lain', 'required', 'when' => function($model){return $model->bagian == self::BAGIAN_LAIN;}, 'whenClient' => "function(){return $('#trnreturpengantar-bagian').val() == '".self::BAGIAN_LAIN."';}"],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED]],

            [['note'], 'string'],
            [['no', 'bagian_lain', 'penerima'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Tanggal',
            'no_urut' => 'No Urut',
            'no' => 'Nomor',
            'bagian' => 'Tujuan Bagian',
            'bagian_lain' => 'Nama Bagian',
            'bagianName' => 'Tujuan Bagian',
            'penerima' => 'Penerima',
            'note' => 'Keterangan',
            'status' => 'Status',
            'posted_at' => 'Posted At',
            'posted_by' => 'Posted By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnReturPengantarItems()
    {
        return $this->hasMany(TrnReturPengantarItem::className(), ['pengantar_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getBagianName()
    {
        if ($this->bagian == self::BAGIAN_LAIN && !empty($this->bagian_lain)) {
            return $this->bagian_lain;
        }
        return self::bagianOptions()[$this->bagian] ?? '-';
    }

    /**
     * Stok hasil retur yang masih ada di gudang jadi dan belum dipakai di surat pengantar draft lain.
     * @param int|null $excludePengantarId surat pengantar yang sedang diubah
     * @return \yii\db\ActiveQuery
     */
    public static function availableStockQuery($excludePengantarId = null)
    {
        $dipakai = TrnReturPengantarItem::find()
            ->alias('pi')
            ->innerJoin(self::tableName() . ' p', 'p.id = pi.pengantar_id')
            ->select('pi.gudang_jadi_id')
            ->where(['p.status' => self::STATUS_DRAFT])
            ->andFilterWhere(['<>', 'p.id', $excludePengantarId]);

        return TrnGudangJadi::find()
            ->where(['trn_gudang_jadi.source' => TrnGudangJadi::SOURCE_RETUR, 'trn_gudang_jadi.status' => TrnGudangJadi::STATUS_STOCK])
            ->andWhere(['not in', 'trn_gudang_jadi.id', $dipakai]);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "SPR/{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

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
