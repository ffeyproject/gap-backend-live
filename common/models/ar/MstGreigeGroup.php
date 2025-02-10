<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mst_greige_group".
 *
 * @property int $id
 * @property int $jenis_kain 1=Suiting 2=Suiting Ladies 3=Printing 4=Kniting 5=Georgette 6=Lain-lain
 * @property string $nama_kain
 * @property float $qty_per_batch
 * @property int $unit 1=YARD 2=METER 3=PCS 4=KILOGRAM
 * @property float $nilai_penyusutan
 * @property string|null $gramasi_kain
 * @property string|null $sulam_pinggir
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property bool|null $aktif
 * @property int $lebar_kain 1=44" 2=58"
 *
 * @property float $qtyFinish
 * @property float $qtyFinishToYard
 *
 * @property string $unitName
 * @property string $jenisKainName
 * @property string $lebarKainName
 *
 * @property MstGreige[] $mstGreiges
 * @property TrnScGreige[] $trnScGreiges
 * @property TrnStockGreige[] $trnStockGreiges
 */
class MstGreigeGroup extends \yii\db\ActiveRecord
{
    const JENIS_KAIN_SUITING = 1; const JENIS_KAIN_PRINTING = 3; const JENIS_KAIN_KNITING = 4; const JENIS_KAIN_GEORGETTE = 5; const JENIS_KAIN_OTHER = 6;
    /**
     * @return array
     */
    public static function jenisKainOptions(){
        return [
            self::JENIS_KAIN_SUITING => 'Suiting',
            self::JENIS_KAIN_PRINTING => 'Printing',
            self::JENIS_KAIN_KNITING => 'Kniting',
            self::JENIS_KAIN_GEORGETTE => 'Georgette',
            self::JENIS_KAIN_OTHER => 'Lain-lain',
        ];
    }

    const UNIT_YARD = 1; const UNIT_METER = 2; const UNIT_PCS = 3; const UNIT_KILOGRAM = 4;
    /**
     * @return array
     */
    public static function unitOptions(){
        return [
            self::UNIT_YARD => 'Yard',
            self::UNIT_METER => 'Meter',
            self::UNIT_PCS => 'Pcs',
            self::UNIT_KILOGRAM => 'Kilogram',
        ];
    }

    const LEBAR_KAIN_44 = 1; const LEBAR_KAIN_58 = 2; const LEBAR_KAIN_60 = 3; const LEBAR_KAIN_64 = 4; const LEBAR_KAIN_66 = 5; const LEBAR_KAIN_68 = 6;
    /**
     * @return array
     */
    public static function lebarKainOptions(){
        return [
            self::LEBAR_KAIN_44 => '44"',
            self::LEBAR_KAIN_58 => '58"',
            self::LEBAR_KAIN_60 => '60"',
            self::LEBAR_KAIN_64 => '64"',
            self::LEBAR_KAIN_66 => '66"',
            self::LEBAR_KAIN_68 => '68"',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_greige_group';
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
            [['jenis_kain', 'nama_kain', 'unit', 'lebar_kain'], 'required'],
            [['jenis_kain', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by', 'lebar_kain'], 'default', 'value' => null],
            [['jenis_kain', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by', 'lebar_kain'], 'integer'],
            [['qty_per_batch', 'nilai_penyusutan'], 'number'],
            [['aktif'], 'boolean'],
            [['nama_kain', 'sulam_pinggir'], 'string', 'max' => 255],
            [['gramasi_kain'], 'string', 'max' => 150],

            ['jenis_kain', 'default', 'value'=>self::JENIS_KAIN_SUITING],
            ['jenis_kain', 'in', 'range' => [self::JENIS_KAIN_SUITING, self::JENIS_KAIN_PRINTING, self::JENIS_KAIN_KNITING, self::JENIS_KAIN_GEORGETTE, self::JENIS_KAIN_OTHER]],

            ['unit', 'default', 'value'=>self::UNIT_YARD],
            ['unit', 'in', 'range' => [self::UNIT_YARD, self::UNIT_METER, self::UNIT_PCS, self::UNIT_KILOGRAM]],

            ['lebar_kain', 'default', 'value'=>self::LEBAR_KAIN_44],
            ['lebar_kain', 'in', 'range' => [self::LEBAR_KAIN_44, self::LEBAR_KAIN_58, self::LEBAR_KAIN_60, self::LEBAR_KAIN_64, self::LEBAR_KAIN_66, self::LEBAR_KAIN_68]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_kain' => 'Jenis Kain',
            'lebar_kain' => 'Lebar Kain',
            'nama_kain' => 'Nama Kain',
            'qty_per_batch' => 'Qty Per Batch',
            'unit' => 'Unit',
            'nilai_penyusutan' => 'Nilai Penyusutan',
            'gramasi_kain' => 'Gramasi Kain',
            'sulam_pinggir' => 'Sulam Pinggir',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'aktif' => 'Aktif',
            'lebarKainName' => 'Lebar Kain'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMstGreiges()
    {
        return $this->hasMany(MstGreige::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScGreiges()
    {
        return $this->hasMany(TrnScGreige::className(), ['greige_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnStockGreiges()
    {
        return $this->hasMany(TrnStockGreige::className(), ['greige_group_id' => 'id']);
    }

    /**
     * @return float
     */
    public function getQtyFinish()
    {
        $perBatch = (float)$this->qty_per_batch;
        $susut = (float)$this->nilai_penyusutan;

        return $perBatch * (1 - ($susut/100));
    }

    /**
     * @return float
     */
    public function getQtyFinishToYard(){
        if($this->unit == self::UNIT_METER){
            return Converter::meterToYard($this->qtyFinish);
        }else{
            return $this->qtyFinish;
        }
    }

    /**
     * @return float
     */
    public function getQtyFinishToMeter(){
        if($this->unit == self::UNIT_YARD){
            return Converter::yardToMeter($this->qtyFinish);
        }else{
            return $this->qtyFinish;
        }
    }

    /**
     * @return string
     */
    public function getUnitName(){
        return self::unitOptions()[$this->unit];
    }

    /**
     * @return string
     */
    public function getJenisKainName(){
        return self::jenisKainOptions()[$this->jenis_kain];
    }

    /**
     * @return string
     */
    public function getLebarKainName()
    {
        return self::lebarKainOptions()[$this->lebar_kain];
    }
}