<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_wo".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $jenis_order Pilihan jenis order sama dengan (mereferensi) jenis order pada SC
 * @property int $greige_id Greige yang digunakan berdasarkan greige_group pada tabel sc_greige
 * @property int $mengetahui_id
 * @property int|null $apv_mengetahui_at
 * @property string|null $reject_note_mengetahui
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property int $papper_tube_id 1=113x3,8 2=113x5,0 3=150x3,8 4=150x5,0 5=160x3,2 6=122x3,2 7=Lainnya
 * @property string|null $plastic_size
 * @property string|null $shipping_mark
 * @property string|null $note
 * @property string|null $note_two
 * @property int $marketing_id
 * @property int|null $apv_marketing_at
 * @property string|null $reject_note_marketing
 * @property int|null $posted_at
 * @property int|null $closed_at
 * @property int|null $closed_by
 * @property string|null $closed_note
 * @property int|null $batal_at
 * @property int|null $batal_by
 * @property string|null $batal_note
 * @property int $status 1=draft, 2=posted, 3=approved by mengetahui, 4=approved by marketing, 5=approved, 6=rejected, 7=closed, 8=batal
 * @property bool $validasi_stock Jika false, maka tidak akan dilakukan validasi stock ketika proses approval.
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $handling_id
 * @property string|null $tgl_kirim
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeingsNonPg
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintingsNonPg
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
 * @property MstGreige $greige
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property MstHandling $handling
 * @property TrnScGreige $scGreige
 * @property User $mengetahui
 * @property User $marketing
 * @property User $closedBy
 * @property User $batalBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnWoColor[] $trnWoColors
 * @property TrnWoMemo[] $trnWoMemos
 * @property MstPapperTube $papperTube
 *
 * @property string $jenisOrderName
 * @property string $greigeNamaKain
 * @property string $mengetahuiName
 * @property string $marketingName
 * @property string $creatorName
 * @property string $updatorName
 * @property string $scNo
 * @property string $greigeGroupNamaKain
 *
 * @property float $colorQty
 * @property float $colorQtyBatchToUnit
 * @property float $colorQtyBatchToMeter
 * @property float $colorQtyFinish
 * @property float $colorQtyFinishToMeter
 * @property float $colorQtyFinishToYard
 */
class TrnWo extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APV_MENNGETAHUI = 3;const STATUS_APV_MARKETING = 4;const STATUS_APPROVED = 5;const STATUS_REJECTED = 6;const STATUS_CLOSED = 7;const STATUS_BATAL = 8;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Diposting',
            self::STATUS_APV_MENNGETAHUI => 'Disetujui Kabag PMC',
            self::STATUS_APV_MARKETING => 'Disetujui Marketing',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_BATAL => 'Batal',
        ];
    }
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_wo';
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
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'date', 'papper_tube_id', 'marketing_id', 'handling_id'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'papper_tube_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'papper_tube_id'], 'integer'],
            [['reject_note_mengetahui', 'shipping_mark', 'note', 'note_two', 'reject_note_marketing', 'closed_note', 'batal_note'], 'string'],
            [['date', 'tgl_kirim'], 'date', 'format'=>'php:Y-m-d'],
            [['no', 'plastic_size'], 'string', 'max' => 255],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['mengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['mengetahui_id' => 'id']],
            [['marketing_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['marketing_id' => 'id']],
            [['closed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['closed_by' => 'id']],
            [['batal_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['batal_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
            [['papper_tube_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstPapperTube::className(), 'targetAttribute' => ['papper_tube_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'jenis_order' => 'Jenis Order',
            'greige_id' => 'Greige ID',
            'mengetahui_id' => 'Mengetahui ID',
            'apv_mengetahui_at' => 'Apv Mengetahui At',
            'reject_note_mengetahui' => 'Reject Note Mengetahui',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'papper_tube_id' => 'Peper Tube',
            'plastic_size' => 'Plastic Size',
            'shipping_mark' => 'Shipping Mark',
            'note' => 'Note',
            'note_two' => 'Note Two',
            'marketing_id' => 'Marketing ID',
            'apv_marketing_at' => 'Apv Marketing At',
            'reject_note_marketing' => 'Reject Note Marketing',
            'posted_at' => 'Posted At',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'closed_note' => 'Closed Note',
            'batal_at' => 'Batal At',
            'batal_by' => 'Batal By',
            'batal_note' => 'Batal Note',
            'tgl_kirim' => 'Tanggal Kirim',
            'status' => 'Status',
            'validasi_stock' => 'Validasi Stock',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'handling_id' => 'Handling ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['wo_id' => 'id']);
    }

    /**
     * Kartu proses yang tidak gagal (tidak penggantian greige)
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingsNonPg()
    {
        return $this->getTrnKartuProsesDyeings()->where(['not in', 'status', [TrnKartuProsesDyeing::STATUS_GANTI_GREIGE, TrnKartuProsesDyeing::STATUS_GANTI_GREIGE_LINKED, TrnKartuProsesDyeing::STATUS_BATAL]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['wo_id' => 'id']);
    }

    /**
     * Kartu proses yang tidak gagal (tidak penggantian greige)
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingsNonPg()
    {
        return $this->getTrnKartuProsesPrintings()->where(['not in', 'status', [TrnKartuProsesPrinting::STATUS_GANTI_GREIGE, TrnKartuProsesPrinting::STATUS_GANTI_GREIGE_LINKED]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['wo_id' => 'id']);
    }

    public function getTrnGreigeKeluarMakloon()
    {
        return $this->hasMany(TrnGreigeKeluar::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPapperTube()
    {
        return $this->hasOne(MstPapperTube::className(), ['id' => 'papper_tube_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[Handling0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandling()
    {
        return $this->hasOne(MstHandling::className(), ['id' => 'handling_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMengetahui()
    {
        return $this->hasOne(User::className(), ['id' => 'mengetahui_id']);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketing()
    {
        return $this->hasOne(User::className(), ['id' => 'marketing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClosedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'closed_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatalBy()
    {
        return $this->hasOne(User::className(), ['id' => 'batal_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['wo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoMemos()
    {
        return $this->hasMany(TrnWoMemo::className(), ['wo_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getJenisOrderName()
    {
        return TrnSc::jenisOrderOptions()[$this->jenis_order];
    }

    /**
     * @return string
     */
    public function getGreigeNamaKain()
    {
        return $this->greige->nama_kain;
    }

    /**
     * @return string
     */
    public function getMengetahuiName()
    {
        return $this->mengetahui->full_name;
    }

    /**
     * @return string
     */
    public function getMarketingName()
    {
        return $this->marketing->full_name;
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->createdBy->full_name;
    }

    /**
     * @return string
     */
    public function getUpdatorName()
    {
        return $this->updatedBy->full_name;
    }

    /**
     * @return string
     */
    public function getGreigeGroupNamaKain()
    {
        return $this->scGreige->greigeGroup->nama_kain;
    }

    /**
     * @return float
     */
    public function getColorQty()
    {
        $qty = $this->getTrnWoColors()->sum('qty');
        return $qty === null ? 0 : $qty;
    }

    /**
     * @return float
     */
    public function getColorQtyBatchToUnit()
    {
        return $this->colorQty * $this->scGreige->greigeGroup->qty_per_batch;
    }

    /**
     * @return float
     */
    public function getColorQtyBatchToMeter()
    {
        $qty = $this->colorQty * $this->scGreige->greigeGroup->qty_per_batch;

        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($qty);
                break;
            case MstGreigeGroup::UNIT_METER:
                return $qty;
                break;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getColorQtyBatchToYard()
    {
        $qty = $this->colorQty * $this->scGreige->greigeGroup->qty_per_batch;

        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return $qty;
                break;
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($qty);
                break;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getColorQtyFinish()
    {
        return ($this->colorQty * $this->scGreige->greigeGroup->qtyFinish) + (float)$this->greige->gap;
    }

    /**
     * @return float
     */
    public function getColorQtyFinishToMeter()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->colorQtyFinish);
                break;
            case MstGreigeGroup::UNIT_METER:
                return $this->colorQtyFinish;
                break;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getColorQtyFinishToYard()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return $this->colorQtyFinish;
                break;
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->colorQtyFinish);
                break;
            default:
                return 0;
        }
    }
    
    /**
     * @return int
     */
    /*
     * Jumlah kartu proses dyeing yang belum di close
     * (bukaan) berdasarkan TrnWo ini.
     */
    public function getKartuProsesDyeingBukaan()
    {
        $kartuProsesDyeings = $this->getTrnKartuProsesDyeings();
        $count = 0;
    
        foreach ($kartuProsesDyeings->all() as $kartuProses) {
            $tanggal = $kartuProses->getTanggalKartuProcessDyeingProcess();
            if ($tanggal !== null) {
                $count++;
            }
        }
    
        return $count;
    }
    

    private function setNoUrut(){
        $scGreige = $this->scGreige;

        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        //$m = date("n", $ts); //bulan satu digit 1-12
        //$ym = $y.'-'.$m;

        /* @var $lastData TrnWo*/
        $lastData = TrnWo::find()->joinWith('mo.scGreige')
            ->select(['trn_wo.no_urut', 'trn_wo.mo_id'])
            ->where([
                'and',
                ['not', ['trn_wo.no_urut' => null]],
                new Expression('EXTRACT(year FROM "'.self::tableName().'"."date") = '.$y), //nomor per tahun
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
                ['trn_wo.jenis_order' => $this->jenis_order],
                ['trn_sc_greige.process' => $scGreige->process],
            ])
            ->orderBy(['trn_wo.no_urut' => SORT_DESC])
            ->asArray()
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            /*switch ($this->scGreige->process){
                case TrnScGreige::PROCESS_DYEING:
                    $this->no_urut = 1045;
                    break;
                case TrnScGreige::PROCESS_PRINTING:
                    $this->no_urut = 491;
                    break;
                default:
                    $this->no_urut = 1;
            }*/
            $this->no_urut = 1;
        }
    }

    public function setNoWo(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $scGreige = $this->scGreige;

        switch ($scGreige->process){
            case TrnScGreige::PROCESS_DYEING:
                $jenisProsesCode = 'D';
                break;
            case TrnScGreige::PROCESS_PRINTING:
                $jenisProsesCode = 'P';
                break;
            /*case TrnScGreige::PROCESS_GREIGE:
                $jenisProsesCode = 'G';
                break;*/
            case TrnScGreige::PROCESS_PFP:
                $jenisProsesCode = 'F';
                break;
            /*case TrnScGreige::PROCESS_MAKLON:
                $jenisProsesCode = 'M';
                break;*/
            default:
                $jenisProsesCode = '-';
        }

        switch ($this->jenis_order){
            case TrnSc::JENIS_ORDER_FRESH_ORDER:
                $jenisOrderCode = '0';
                break;
            case TrnSc::JENIS_ORDER_MAKLOON:
                $jenisOrderCode = '1';
                break;
            case TrnSc::JENIS_ORDER_BARANG_JADI:
                $jenisOrderCode = '2';
                break;
            case TrnSc::JENIS_ORDER_STOK:
                $jenisOrderCode = '3';
                break;
            default:
                $jenisOrderCode = '-';
        }

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);

        switch ($scGreige->sc->tipe_kontrak){
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $tipeKontrak = 'L';
                break;
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $tipeKontrak = 'E';
                break;
            default:
                $tipeKontrak = '-';
        }

        $this->no = "{$jenisProsesCode}{$yearTwoDigit}{$month}/{$jenisOrderCode}{$noUrut}{$tipeKontrak}";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnGreigeKeluar()
    {
        return $this->hasMany(TrnGreigeKeluar::className(), ['wo_id' => 'id']);
    }


    /**
     * @return float
     */
    public function getTotalPanjangGreigeKeluar()
    {   
        $greigeKeluars = $this->trnGreigeKeluar;
        $totalPanjang = 0;
        foreach ($greigeKeluars as $greigeKeluar) {
            $stockGreiges = $greigeKeluar->stockGreiges;
            foreach ($stockGreiges as $stockGreige) {
                $totalPanjang += $stockGreige->panjang_m;
            }
        }
        return $totalPanjang === null ? 0 : $totalPanjang;
    }
}