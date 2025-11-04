<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_stock_greige".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $asal_greige 1=Water Jet Loom, 2=Beli Lokal, 3=Rapier, 4=Beli Import, 5=Lain-lain
 * @property string $no_lapak
 * @property int $grade 1=A,2=B,3=C,4=D,5=E
 * @property string $lot_lusi
 * @property string $lot_pakan
 * @property string $no_set_lusi
 * @property float $panjang_m kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)
 * @property int $status_tsd 1=sm(salur muda),2=st(salur tua),3=sa(salur abnormal),7=Putih
 * @property string $no_document
 * @property string $pengirim
 * @property string $mengetahui
 * @property string|null $note
 * @property int $status 1=Pending, 2=Valid, 3=On Process Card, 4=Dipotong, 5=Dikeluarkan Dari Gudang
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $jenis_gudang 1=Gudang Fresh, 2=Gudang WIP, 3=Gudang PFP, 4=Gudang Ex Finish
 * @property string|null $nomor_wo
 * @property int|null $keputusan_qc
 * @property string|null $color
 * @property int|null $pfp_jenis_gudang Pembagian jenis gudang untuk kain PFP, 1=Fudang 1, 2=Gudang 2
 * @property boolean $is_pemotongan
 * @property boolean $is_hasil_mix
 *
 * @property string $jenisGudangName
 * @property string $pfpJenisGudangName
 *
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 *
 * @property string $greigeNamaKain
 * @property string $gradeName
 * @property string $kondisiGreige
 */
class TrnStockGreige extends \yii\db\ActiveRecord
{
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

    const STATUS_TSD_SM = 1;const STATUS_TSD_ST = 2;const STATUS_TSD_SA = 3;const STATUS_TSD_NORMAL = 4;const STATUS_TSD_LAIN_LAIN = 5;const STATUS_TSD_TSD = 6;const STATUS_TSD_PUTIH = 7;const STATUS_GETAR_MESIN = 8;
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
            self::STATUS_TSD_PUTIH => 'Putih',
            self::STATUS_GETAR_MESIN => 'Getaran Mesin'
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_stock_greige';
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
            [['greige_id', 'grade', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'panjang_m', 'status_tsd', 'no_document', 'pengirim', 'mengetahui'], 'required'],
            [['greige_group_id', 'greige_id', 'asal_greige', 'panjang_m', 'created_at', 'created_by', 'updated_at', 'updated_by', 'keputusan_qc'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'asal_greige', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['panjang_m', 'number'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],

            [['is_hasil_mix', 'is_pemotongan'], 'boolean'],
            [['is_hasil_mix', 'is_pemotongan'], 'default', 'value'=>false],

            ['grade', 'default', 'value'=>self::GRADE_A],
            ['grade', 'in', 'range' => [self::GRADE_A, self::GRADE_B, self::GRADE_C, self::GRADE_D, self::GRADE_E, self::GRADE_NG, self::GRADE_A_PLUS, self::GRADE_A_ASTERISK, self::GRADE_PUTIH]],

            ['status', 'default', 'value'=>self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_VALID, self::STATUS_ON_PROCESS_CARD, self::STATUS_ON_PROCESS_CARD, self::STATUS_DIPOTONG, self::STATUS_KELUAR_GUDANG]],

            ['jenis_gudang', 'default', 'value'=>self::JG_FRESH],
            ['jenis_gudang', 'in', 'range' => [self::JG_FRESH, self::JG_WIP, self::JG_PFP, self::JG_EX_FINISH]],

            ['pfp_jenis_gudang', 'in', 'range' => [self::PFP_JG_ONE, self::PFP_JG_TWO]],

            ['status_tsd', 'default', 'value'=>self::STATUS_TSD_SM],
            ['status_tsd', 'in', 'range' => [self::STATUS_TSD_SM, self::STATUS_TSD_ST, self::STATUS_TSD_SA, self::STATUS_TSD_NORMAL, self::STATUS_TSD_LAIN_LAIN, self::STATUS_TSD_TSD, self::STATUS_TSD_PUTIH]],

            ['keputusan_qc', 'in', 'range' => [TrnReturBuyer::QC_DRAFT, TrnReturBuyer::QC_GOOD, TrnReturBuyer::QC_REPAIR, TrnReturBuyer::QC_REJECT]],

            ['asal_greige', 'in', 'range' => [self::ASAL_GREIGE_WJL, self::ASAL_GREIGE_BELI, self::ASAL_GREIGE_RAPIER, self::ASAL_GREIGE_BELI_IMPORT, self::ASAL_GREIGE_LAIN_LAIN, self::ASAL_GREIGE_RETUR, self::ASAL_GREIGE_MUTASI, self::ASAL_GREIGE_PEMOTONGAN, self::ASAL_GREIGE_MAKLOON]],

            ['no_lapak', 'default', 'value'=>'-'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'no_document', 'pengirim', 'mengetahui', 'nomor_wo', 'color'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'asal_greige' => 'Asal Greige',
            'no_lapak' => 'No Lapak',
            'grade' => 'Grade',
            'lot_lusi' => 'Lot Lusi',
            'lot_pakan' => 'Lot Pakan',
            'no_set_lusi' => 'No. MC Weaving',
            'panjang_m' => 'Qty',
            'status_tsd' => 'Ket. Weaving',
            'no_document' => 'No Document',
            'pengirim' => 'Pengirim',
            'mengetahui' => 'Mengetahui',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'jenis_gudang' => 'Jenis Gudang',
            'nomor_wo' => 'Nomor WO',
            'keputusan_qc' => 'Keputusan QC dan Marketing',
            'color' => 'Color',
            'pfp_jenis_gudang' => 'Jenis Gudang PFP',
            'jenisGudangName' => 'Jenis Gudang',
            'pfpJenisGudangName' => 'Jenis Gudang PFP',
            'is_pemotongan' => 'Hasil Pemotongan',
            'is_hasil_mix' => 'Mix Quality',
            'greigeNamaKain' => 'Motif',
            'gradeName' => 'Grade',
            'kondisiGreige' => 'Kondisi Greige',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['stock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['stock_id' => 'id']);
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
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
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
    public function getGradeName()
    {
        return self::gradeOptions()[$this->grade];
    }

    /**
     * @return string
     */
    public function getKondisiGreige()
    {
        return self::tsdOptions()[$this->status_tsd];
    }

     /**
     * @return string
     */
    public function getAsalGreige()
    {
        return self::asalGreigeOptions()[$this->asal_greige];
    }
    
    /**
     * @return string
     */
    public static function getStockPerGrade($greigeId)
    {
        $query = self::find()
            ->select(['grade', 'SUM(panjang_m) AS total'])
            ->where(['greige_id' => $greigeId, 'status' => self::STATUS_VALID, 'jenis_gudang' => self::JG_FRESH])
            ->groupBy('grade')
            ->asArray()
            ->all();
        $total = 0;
        $stockPerGrade = [];
        foreach ($query as $item) {
            $stockPerGrade[$item['grade']] = $item['total'];
            $total += $item['total'];
        }
        $stockPerGrade['total'] = $total;

        return $stockPerGrade;
    }


    public function rollbackToValid()
    {
        $this->status = self::STATUS_VALID;
        return $this->save(false, ['status']);
    }

    public function getOpname()
    {
        return $this->hasOne(TrnStockGreigeOpname::class, ['stock_greige_id' => 'id']);
    }

    public function getIsDuplicated()
    {
        return $this->getOpname()->exists();
    }

}