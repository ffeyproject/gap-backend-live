<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_gudang_jadi_opname_pcs".
 *
 * @property int $id
 * @property int|null $id_trn_gudang_jadi Relasi ke trn_gudang_jadi(id)
 * @property string $opname_code Nomor / Kode Dokumen Opname
 * @property string $qr_code QR Code Barang Pcs Roll
 * @property string|null $qr_code_desc Deskripsi / Spesifikasi Kain Pcs Roll
 * @property float $qty Jumlah Yard / Meter
 * @property string $unit Satuan Barang
 * @property int $grade 1=Grade A, 2=Grade B, 3=Grade C, dst
 * @property string|null $join_piece Keterangan Join Piece
 * @property string $locs_code Kode Lokasi Gudang
 * @property int $status 1=Draft/Submitted, 2=Verified
 * @property string|null $remark Catatan Tambahan
 * @property int $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnGudangJadi $gudangJadi
 */
class TrnGudangJadiOpnamePcs extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_VERIFIED = 2;

    /**
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Draft / Submitted',
            self::STATUS_VERIFIED => 'Verified',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_gudang_jadi_opname_pcs';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['opname_code', 'qr_code', 'qty'], 'required'],
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['qty'], 'number'],
            [['qr_code_desc', 'remark'], 'string'],
            [['opname_code', 'qr_code'], 'string', 'max' => 100],
            [['unit'], 'string', 'max' => 20],
            [['join_piece', 'locs_code'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_VERIFIED]],
            [['locs_code'], 'default', 'value' => 'TRANSIT'],
            [['unit'], 'default', 'value' => 'YARDS'],
            [['grade'], 'default', 'value' => 1],
            [['id_trn_gudang_jadi'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['id_trn_gudang_jadi' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_trn_gudang_jadi' => 'ID Gudang Jadi',
            'opname_code' => 'Kode Opname',
            'qr_code' => 'QR Code',
            'qr_code_desc' => 'Deskripsi QR Code',
            'qty' => 'Qty',
            'unit' => 'Satuan',
            'grade' => 'Grade',
            'join_piece' => 'Join Piece',
            'locs_code' => 'Kode Lokasi',
            'status' => 'Status',
            'remark' => 'Catatan',
            'created_at' => 'Dibuat Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_at' => 'Diubah Pada',
            'updated_by' => 'Diubah Oleh',
        ];
    }

    /**
     * Gets query for [[GudangJadi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadi()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'id_trn_gudang_jadi']);
    }

    /**
     * Helper method for Grade Label
     * @return string
     */
    public function getGradeName()
    {
        return isset(TrnStockGreige::gradeOptions()[$this->grade]) ? TrnStockGreige::gradeOptions()[$this->grade] : 'Grade ' . $this->grade;
    }

    /**
     * Helper method for Status Label
     * @return string
     */
    public function getStatusName()
    {
        return isset(self::statusOptions()[$this->status]) ? self::statusOptions()[$this->status] : '-';
    }

    /**
     * Helper method for Unit Label
     * @return string
     */
    public function getUnitName()
    {
        if (is_numeric($this->unit) && isset(MstGreigeGroup::unitOptions()[(int)$this->unit])) {
            return MstGreigeGroup::unitOptions()[(int)$this->unit];
        }
        $uUpper = strtoupper(trim((string)$this->unit));
        if (in_array($uUpper, ['1', 'YARD', 'YARDS', 'YD'])) return 'Yard';
        if (in_array($uUpper, ['2', 'METER', 'METERS', 'MTR', 'M'])) return 'Meter';
        if (in_array($uUpper, ['3', 'PCS', 'PIECE', 'PIECES'])) return 'Pcs';
        if (in_array($uUpper, ['4', 'KILOGRAM', 'KG', 'KILOGRAMS'])) return 'Kilogram';
        return !empty($this->unit) ? $this->unit : '-';
    }
}
