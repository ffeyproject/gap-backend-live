<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Model untuk table trn_stock_greige_opname
 *
 * @property int $id
 * @property int $stock_greige_id
 * @property int $greige_id
 * @property int $greige_group_id
 * @property int $asal_greige
 * @property string $no_lapak
 * @property int $grade
 * @property string $lot_lusi
 * @property string $lot_pakan
 * @property string $no_set_lusi
 * @property float $panjang_m
 * @property int $status_tsd 1=sm(salur muda),2=st(salur tua),3=sa(salur abnormal),7=Putih
 * @property string $no_document
 * @property string $pengirim
 * @property string $mengetahui
 * @property string|null $note
 * @property int $status
 * @property string $date
 * @property int $jenis_gudang
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnStockGreige $stockGreige
 * @property MstGreigeGroup $greigeGroup
 */
class TrnStockGreigeOpname extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'trn_stock_greige_opname';
    }

    public function getJenisGudangName()
    {
        return self::jenisGudangOptions()[$this->jenis_gudang];
    }

    /**
     * @return string
     */
    public function getPfpJenisGudangName()
    {
        return $this->pfp_jenis_gudang !== null ? self::pfpJenisGudangOptions()[$this->pfp_jenis_gudang] : '';
    }

    const ASAL_GREIGE_WJL = 1;
    const ASAL_GREIGE_BELI = 2;
    const ASAL_GREIGE_RAPIER = 3;
    const ASAL_GREIGE_BELI_IMPORT = 4;
    const ASAL_GREIGE_LAIN_LAIN = 5;
    const ASAL_GREIGE_RETUR = 6;
    const ASAL_GREIGE_MUTASI = 7;
    const ASAL_GREIGE_PEMOTONGAN = 8;
    const ASAL_GREIGE_MAKLOON = 9;
    /**
     * @return array
     */
    public static function asalGreigeOptions(){
        return [
            self::ASAL_GREIGE_WJL => 'Water Jet Loom',
            self::ASAL_GREIGE_BELI => 'Beli Lokal',
            self::ASAL_GREIGE_RAPIER => 'Rapier Loom',
            self::ASAL_GREIGE_BELI_IMPORT => 'Beli Import',
            self::ASAL_GREIGE_LAIN_LAIN => 'Lain-lain',
            self::ASAL_GREIGE_RETUR => 'Retur',
            self::ASAL_GREIGE_MUTASI => 'Mutasi',
            self::ASAL_GREIGE_PEMOTONGAN => 'Pemotongan',
            self::ASAL_GREIGE_MAKLOON => 'Hasil Makloon'
        ];
    }
    
    
      const GRADE_A = 1;const GRADE_B = 2;const GRADE_C = 3;const GRADE_D = 4;const GRADE_E = 5;const GRADE_NG = 6;const GRADE_A_PLUS = 7;const GRADE_A_ASTERISK = 8; const GRADE_PUTIH = 9;
     public static function gradeOptions(){
        return [
            self::GRADE_A => 'A', self::GRADE_B => 'B', self::GRADE_C => 'C', self::GRADE_D => 'D', self::GRADE_E => 'E', self::GRADE_NG => 'NG', self::GRADE_A_PLUS => 'A+', self::GRADE_A_ASTERISK => 'A*', self::GRADE_PUTIH => 'Putih',
        ];
    }

     const STATUS_TSD_SM = 1;const STATUS_TSD_ST = 2;const STATUS_TSD_SA = 3;const STATUS_TSD_NORMAL = 4;const STATUS_TSD_LAIN_LAIN = 5;const STATUS_TSD_TSD = 6;const STATUS_TSD_PUTIH = 7;
    /**
     * @return array
     */
    public static function tsdOptions(){
        return [
            self::STATUS_TSD_SM => 'Salur Muda',
            self::STATUS_TSD_ST => 'Salur Tua',
            self::STATUS_TSD_SA => 'Salur Abnormal',
            self::STATUS_TSD_NORMAL => 'Normal',
            self::STATUS_TSD_LAIN_LAIN => 'Lain-lain',
            self::STATUS_TSD_TSD=>'TSD',
            self::STATUS_TSD_PUTIH => 'Putih'
        ];
    }

    const STATUS_PENDING = 1;
    const STATUS_VALID = 2;
    const STATUS_ON_PROCESS_CARD = 3; //diinput ke kartu proses
    const STATUS_DIPOTONG = 4;
    const STATUS_KELUAR_GUDANG = 5;
    const STATUS_MIXED = 6;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_PENDING => 'Pending', self::STATUS_VALID => 'Valid', self::STATUS_ON_PROCESS_CARD => 'Masuk Kartu Proses', self::STATUS_DIPOTONG => 'Dipotong', self::STATUS_KELUAR_GUDANG => 'Dikeluarkan Dari Gudang', self::STATUS_MIXED => 'Di Mix'];
    }

    const JG_FRESH = 1;const JG_WIP = 2;const JG_PFP = 3; const JG_EX_FINISH = 4;
    /**
     * @return array
     */
    public static function jenisGudangOptions(){
        return [self::JG_FRESH => 'Fresh', self::JG_WIP => 'WIP', self::JG_PFP => 'PFP', self::JG_EX_FINISH => 'EX Finish'];
    }

    const PFP_JG_ONE = 1;const PFP_JG_TWO = 2;
    /**
     * @return array
     */
    public static function pfpJenisGudangOptions(){
        return [self::PFP_JG_ONE => 'Gudang 1', self::PFP_JG_TWO => 'Gudang 2'];
    }

    

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['stock_greige_id', 'greige_id', 'greige_group_id', 'asal_greige', 'no_lapak', 'grade', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'panjang_m', 'status_tsd', 'no_document', 'pengirim', 'mengetahui', 'date'], 'required'],
            [['stock_greige_id', 'greige_id', 'greige_group_id', 'asal_greige', 'grade', 'status_tsd', 'status', 'jenis_gudang', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['panjang_m'], 'number'],
            [['note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'no_document', 'pengirim', 'mengetahui'], 'string', 'max'=>255],
            [['stock_greige_id'], 'exist', 'skipOnError'=>true, 'targetClass'=>TrnStockGreige::class, 'targetAttribute'=>['stock_greige_id'=>'id']],
            [['greige_group_id'], 'exist', 'skipOnError'=>true, 'targetClass'=>MstGreigeGroup::class, 'targetAttribute'=>['greige_group_id'=>'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_greige_id' => 'ID Stock Greige',
            'greige_id' => 'Greige',
            'greige_group_id' => 'Greige Group',
            'asal_greige' => 'Asal Greige',
            'no_lapak' => 'No Lapak',
            'grade' => 'Grade',
            'lot_lusi' => 'Lot Lusi',
            'lot_pakan' => 'Lot Pakan',
            'no_set_lusi' => 'No Set Lusi',
            'panjang_m' => 'Panjang (M)',
            'status_tsd' => 'Status TSD',
            'no_document' => 'No Document',
            'pengirim' => 'Pengirim',
            'mengetahui' => 'Mengetahui',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'jenis_gudang' => 'Jenis Gudang',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getStockGreige()
    {
        return $this->hasOne(TrnStockGreige::class, ['id'=>'stock_greige_id']);
    }

    public function getGreige()
{
    // sesuaikan nama model greige kamu, misalnya MstGreige atau TrnStockGreige
    return $this->hasOne(MstGreige::class, ['id' => 'greige_id']);
}

    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::class, ['id'=>'greige_group_id']);
    }

    public function getGreigeNamaKain()
    {
        return $this->greige ? $this->greige->nama_kain : '-';
    }

    public static function adaOpnameUntuk($stockGreigeId)
    {
        return static::find()
            ->where(['stock_greige_id' => $stockGreigeId])
            ->exists();
    }
    
}