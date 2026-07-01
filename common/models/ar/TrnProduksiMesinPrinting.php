<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "trn_produksi_mesin_printing".
 *
 * @property int $id
 * @property string $jenis_input
 * @property string $tanggal
 * @property string $shift
 * @property string $pembagian_hari
 * @property string|null $start
 * @property string|null $stop
 * @property int $mst_mesin_proses_id
 * @property int|null $kartu_proses_id
 * @property int|null $wo_id
 * @property string|null $wo_no
 * @property string|null $nk_no
 * @property string|null $design
 * @property string|null $motif
 * @property string|null $warna
 * @property string|null $jumlah_pesanan
 * @property string|null $realisasi
 * @property string|null $kurang
 * @property string|null $panjang_greige
 * @property string|null $panjang_jadi
 * @property string|null $keterangan
 * @property int|null $mst_jenis_hambatan_id
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstMesinProses $mstMesinProses
 * @property TrnKartuProsesPrinting $kartuProses
 * @property TrnWo $wo
 * @property MstJenisHambatan $mstJenisHambatan
 * @property User $createdBy
 * @property User $updatedBy
 */
class TrnProduksiMesinPrinting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_produksi_mesin_printing';
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
            [['jenis_input', 'tanggal', 'shift', 'pembagian_hari', 'mst_mesin_proses_id'], 'required'],
            [['mst_mesin_proses_id', 'kartu_proses_id', 'wo_id', 'mst_jenis_hambatan_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['mst_mesin_proses_id', 'kartu_proses_id', 'wo_id', 'mst_jenis_hambatan_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['tanggal'], 'safe'],
            [['keterangan'], 'string'],
            [['jenis_input', 'pembagian_hari', 'start', 'stop', 'jumlah_pesanan', 'realisasi', 'kurang', 'panjang_greige', 'panjang_jadi'], 'string', 'max' => 50],
            [['shift'], 'string', 'max' => 10],
            [['wo_no', 'nk_no', 'design', 'motif', 'warna'], 'string', 'max' => 255],
            [['mst_mesin_proses_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstMesinProses::class, 'targetAttribute' => ['mst_mesin_proses_id' => 'id']],
            [['kartu_proses_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesPrinting::class, 'targetAttribute' => ['kartu_proses_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::class, 'targetAttribute' => ['wo_id' => 'id']],
            [['mst_jenis_hambatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstJenisHambatan::class, 'targetAttribute' => ['mst_jenis_hambatan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_input' => 'Jenis Input',
            'tanggal' => 'Tanggal',
            'shift' => 'Shift',
            'pembagian_hari' => 'Pembagian Hari',
            'start' => 'Start',
            'stop' => 'Stop',
            'mst_mesin_proses_id' => 'No Mesin',
            'kartu_proses_id' => 'Kartu Proses ID',
            'wo_id' => 'WO ID',
            'wo_no' => 'No WO',
            'nk_no' => 'NK',
            'design' => 'Design',
            'motif' => 'Motif',
            'warna' => 'Warna',
            'jumlah_pesanan' => 'Jumlah Pesanan',
            'realisasi' => 'Realisasi',
            'kurang' => 'Kurang',
            'panjang_greige' => 'Con. Inspect',
            'panjang_jadi' => 'Con. Printing',
            'keterangan' => 'Keterangan',
            'mst_jenis_hambatan_id' => 'Jenis Hambatan',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[MstMesinProses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMstMesinProses()
    {
        return $this->hasOne(MstMesinProses::class, ['id' => 'mst_mesin_proses_id']);
    }

    /**
     * Gets query for [[KartuProses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesPrinting::class, ['id' => 'kartu_proses_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::class, ['id' => 'wo_id']);
    }

    /**
     * Gets query for [[MstJenisHambatan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMstJenisHambatan()
    {
        return $this->hasOne(MstJenisHambatan::class, ['id' => 'mst_jenis_hambatan_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'updated_by']);
    }
}
