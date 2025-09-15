<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_gudang_stock_opname".
 *
 * @property int $id
 * @property int|null $greige_group_id
 * @property int $greige_id
 * @property int $asal_greige
 * @property string $no_lapak
 * @property string $lot_lusi
 * @property string $lot_pakan
 * @property int $status_tsd
 * @property string $no_document
 * @property string $operator
 * @property string|null $note
 * @property int $status
 * @property string $date
 * @property int $jenis_gudang
 * @property string|null $nomor_wo
 * @property int|null $keputusan_qc
 * @property string|null $color
 * @property int|null $pfp_jenis_gudang
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property bool $is_pemotongan
 * @property bool $is_hasil_mix
 *
 * @property TrnGudangStockOpnameItem[] $trnGudangStockOpnameItems
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 */
class TrnGudangStockOpname extends ActiveRecord
{   

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
    /**
     * @return array
     */
    public static function gradeOptions(){
        return [
            self::GRADE_A => 'A', self::GRADE_B => 'B', self::GRADE_C => 'C', self::GRADE_D => 'D', self::GRADE_E => 'E', self::GRADE_NG => 'NG', self::GRADE_A_PLUS => 'A+', self::GRADE_A_ASTERISK => 'A*', self::GRADE_PUTIH => 'Putih',
        ];
    }

    const STATUS_TSD_SM = 1;const STATUS_TSD_ST = 2;const STATUS_TSD_SA = 3;const STATUS_TSD_NORMAL = 4;const STATUS_TSD_LAIN_LAIN = 5;const STATUS_TSD_TSD = 6;
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
            self::STATUS_TSD_TSD=>'TSD'
        ];
    }
    const STATUS_DRAFT = 1;
    const STATUS_POSTED = 2;
    const STATUS_OUT = 3;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Masuk', self::STATUS_OUT => 'All Out'];
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


    const JENIS_BELI_LOKAL = 1;const JENIS_BELI_IMPORT = 2;
    /**
     * @return array
     */
    public static function jenisBeliOptions(){
        return [self::JENIS_BELI_LOKAL => 'Beli Lokal', self::JENIS_BELI_IMPORT => 'Beli Import'];
    }

    public static function tableName()
    {
        return 'trn_gudang_stock_opname';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    public function rules()
    {
        return [
            [['greige_id', 'asal_greige', 'no_lapak', 'lot_lusi', 'lot_pakan', 'status_tsd', 'no_document', 'operator', 'date', 'created_at', 'created_by'], 'required'],
            [['greige_group_id', 'greige_id', 'asal_greige', 'status_tsd', 'status', 'jenis_gudang', 'keputusan_qc', 'pfp_jenis_gudang', 'created_at', 'created_by', 'updated_at', 'updated_by','jenis_beli'], 'integer'],
            [['note'], 'string'],
            [['date'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            [['is_pemotongan', 'is_hasil_mix'], 'boolean'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_document', 'operator', 'nomor_wo', 'color'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group',
            'greige_id' => 'Greige',
            'asal_greige' => 'Asal Greige',
            'no_lapak' => 'No Lapak',
            'lot_lusi' => 'Lot Lusi',
            'lot_pakan' => 'Lot Pakan',
            'status_tsd' => 'Status TSD',
            'no_document' => 'No Document',
            'operator' => 'Operator',
            'note' => 'Catatan',
            'status' => 'Status Header',
            'date' => 'Tanggal',
            'jenis_gudang' => 'Jenis Gudang',
            'nomor_wo' => 'Nomor WO',
            'keputusan_qc' => 'Keputusan QC',
            'color' => 'Warna',
            'pfp_jenis_gudang' => 'PFP Jenis Gudang',
            'is_pemotongan' => 'Pemotongan',
            'is_hasil_mix' => 'Hasil Mix',
            'created_at' => 'Dibuat pada',
            'created_by' => 'Dibuat oleh',
            'updated_at' => 'Diperbarui pada',
            'updated_by' => 'Diperbarui oleh',
        ];
    }

    public function getTrnGudangStockOpnameItems()
    {
        return $this->hasMany(TrnGudangStockOpnameItem::class, ['trn_gudang_stock_opname_id' => 'id']);
    }

    public function getTrnGudangStockOpnameItemsNotOut()
    {
        return $this->hasMany(TrnGudangStockOpnameItem::class, ['trn_gudang_stock_opname_id' => 'id'])->where(['is_out' => false]);
    }

    public function getGreige()
    {
        return $this->hasOne(MstGreige::class, ['id' => 'greige_id']);
    }
    public function getGreigeNamaKain()
    {
        return $this->greige->nama_kain;
    }

    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    public function getTrnStockGreige()
    {
        return $this->hasOne(TrnStockGreige::class, ['trn_stock_greige_id' => 'id']);
    }
}