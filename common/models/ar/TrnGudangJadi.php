<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_gudang_jadi".
 *
 * @property int $id
 * @property int|null $jenis_gudang 1=Lokal, 2=Export, 3=Grade B, 4=Verpacking
 * @property int $wo_id
 * @property int $source Asal kain, 1=Packing/Inspecting 2=Makloon Proses 3=Makloon Finish 4=Retur Buyer
 * @property string|null $source_ref Nomor referensi source, misalnya nomor inspecting, no surat terima dari makloon, dll.
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 * @property int $qty Unit menyesuaikan pada kuantiti penerimaan, tidak lagi harus sama dengan greige group
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property int $status 1=stock, 2=out, 3=Siap Kirim, 4=Pembuatan Surat Jalan / Pengantar, 5=Mutasi Ex Finish, 6=Pindah Gudang
 * @property string|null $note
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $color
 * @property string|null $no_memo_repair
 * @property string|null $no_memo_ganti_greige
 * @property int $grade mengacu kepada TrnStockGreige::gradeOptions()
 * @property bool $hasil_pemotongan
 * @property bool $dipotong
 *
 * @property TrnWo $wo
 * @property string $gradeName
 */
class TrnGudangJadi extends \yii\db\ActiveRecord
{
    const JENIS_GUDANG_LOKAL = 1; const JENIS_GUDANG_EXPORT = 2; const JENIS_GUDANG_GRADE_B = 3; const JENIS_GUDANG_VERPACKING = 4;
    /**
     * @return array
     */
    public static function jenisGudangOptions(){
        return [self::JENIS_GUDANG_LOKAL => 'Lokal', self::JENIS_GUDANG_EXPORT => 'Export', self::JENIS_GUDANG_GRADE_B => 'Grade B', self::JENIS_GUDANG_VERPACKING => 'Verpacking'];
    }

    const SOURCE_PACKING = 1; const SOURCE_MAKLOON_PROSES = 2; const SOURCE_MAKLOON_FINISH = 3; const SOURCE_RETUR = 4; const SOURCE_BELI_JADI = 5;
    /**
     * @return array
     */
    public static function sourceOptions(){
        return [self::SOURCE_PACKING => 'Packing/Inspecting', self::SOURCE_MAKLOON_PROSES => 'Makloon Proses', self::SOURCE_MAKLOON_FINISH => 'Makloon Finish', self::SOURCE_RETUR => 'Retur', self::SOURCE_BELI_JADI => 'Beli Jadi'];
    }

    const STATUS_STOCK = 1; const STATUS_OUT = 2; const STATUS_SIAP_KIRIM = 3; const STATUS_SURAT_JALAN = 4; const STATUS_MUTASI_EF = 5; const STATUS_PINDAH_GUDANG = 6;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_STOCK => 'Stock', self::STATUS_OUT => 'Out', self::STATUS_SIAP_KIRIM => 'Siap Kirim', self::STATUS_SURAT_JALAN => 'Proses Surat Jalan', self::STATUS_MUTASI_EF => 'Mutasi Ex Finish', self::STATUS_PINDAH_GUDANG=>'Pindah Gudang'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_gudang_jadi';
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
            [['wo_id', 'source', 'qty', 'date', 'created_at', 'created_by'], 'required'],

            [['wo_id', 'unit', 'qty', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_memo_ganti_greige', 'no_memo_repair'], 'default', 'value' => null],
            [['wo_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],

            ['qty', 'number'],

            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            ['status', 'default', 'value'=>self::STATUS_STOCK],
            ['status', 'in', 'range' => [self::STATUS_STOCK, self::STATUS_OUT, self::STATUS_SIAP_KIRIM, self::STATUS_SURAT_JALAN, self::STATUS_MUTASI_EF, self::STATUS_PINDAH_GUDANG]],

            ['source', 'default', 'value'=>self::SOURCE_PACKING],
            ['source', 'in', 'range' => [self::SOURCE_PACKING, self::SOURCE_MAKLOON_PROSES, self::SOURCE_MAKLOON_FINISH, self::SOURCE_RETUR, self::SOURCE_BELI_JADI]],

            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['jenis_gudang', 'default', 'value'=>self::JENIS_GUDANG_LOKAL],
            ['jenis_gudang', 'in', 'range' => [self::JENIS_GUDANG_LOKAL, self::JENIS_GUDANG_EXPORT, self::JENIS_GUDANG_GRADE_B]],

            [['note'], 'string'],
            [['source_ref', 'no', 'color', 'no_memo_repair', 'no_memo_ganti_greige'], 'string', 'max' => 255],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],

            [['hasil_pemotongan', 'dipotong'], 'boolean'],
            [['hasil_pemotongan', 'dipotong'], 'default', 'value'=>false],

            ['grade', 'default', 'value'=>TrnStockGreige::GRADE_NG],
            ['grade', 'in', 'range' => [TrnStockGreige::GRADE_A, TrnStockGreige::GRADE_B, TrnStockGreige::GRADE_C, TrnStockGreige::GRADE_D, TrnStockGreige::GRADE_E, TrnStockGreige::GRADE_NG, TrnStockGreige::GRADE_A_PLUS, TrnStockGreige::GRADE_A_ASTERISK, TrnStockGreige::GRADE_PUTIH]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_gudang' => 'Jenis Gudang',
            'wo_id' => 'Wo ID',
            'source' => 'Source',
            'source_ref' => 'Source Ref',
            'unit' => 'Unit',
            'qty' => 'Qty',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'status' => 'Status',
            'note' => 'Note',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'no_memo_repair' => 'Nomor Memo Repair',
            'no_memo_ganti_greige' => 'Nomor Memo Ganti Greige',
            'hasil_pemotongan' => 'Hasil Pemotongan',
            'dipotong' => 'Dipotong',
            'gradeName' => 'Grade',
            'locs_code' => 'Location',
        ];
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    public function getInspecting()
    {
        return $this->hasOne(InspectingItem::className(), ['id' => 'id_from']);
        // return $this->hasOne(InspectingItem::className(), ['id' => 'id_from'])->andWhere(['trans_from' => 'INS']);
    }

    public function getInspectingMklbj()
    {
        // return $this->hasOne(InspectingMklBjItems::className(), ['id' => 'id_from'])->andWhere(['trans_from' => 'MKL']);
        return $this->hasOne(InspectingMklBjItems::className(), ['id' => 'id_from']);
    }

    /**
     * @return string
     */
    public function getGradeName()
    {
        return TrnStockGreige::gradeOptions()[$this->grade];
    }

    public static function getLocationAreas() {
        $locations = self::find()->select(['locs_code'])->distinct()->all();
        $options = [];
        foreach ($locations as $location) {
            $options[$location->locs_code] = $location->locs_code;
        }
        
        return $options;
    }

    /**
     * @return string
     */
    /* Returns no_lot of related inspecting or inspecting_mkl_bj, or '-' if no related record found */
    public function getNoLot()
    {
        if ($this->source_ref !== null) {
            $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                ->select('no_lot')
                ->where(['no' => $this->source_ref])
                ->one();
            if ($noLot) {
                return $noLot['no_lot'];
            } else {
                $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                    ->select('no_lot')
                    ->where(['no' => $this->source_ref])
                    ->one();
                if ($noLot) {
                    return $noLot['no_lot'];
                }
            }
        }
        return '-';
    }
}