<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_process_printing".
 *
 * @property int $id
 * @property int $order nomor urutan proses
 * @property int $max_pengulangan
 * @property string $nama_proses
 * @property bool $tanggal
 * @property bool $start
 * @property bool $stop
 * @property bool $no_mesin
 * @property bool $operator
 * @property bool $temp
 * @property bool $speed_depan
 * @property bool $speed_belakang
 * @property bool $speed
 * @property bool $resep
 * @property bool $density
 * @property bool $jumlah_pcs
 * @property bool $lebar_jadi
 * @property bool $panjang_jadi
 * @property bool $info_kualitas
 * @property bool $gangguan_produksi
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $over_feed
 *
 * @property KartuProcessPrintingProcess[] $kartuProcessPrintingProcesses
 * @property TrnKartuProsesPrinting[] $kartuProcesses
 */
class MstProcessPrinting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_process_printing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order', 'nama_proses'], 'required'],
            [['order', 'max_pengulangan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['order', 'max_pengulangan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['tanggal', 'start', 'stop', 'no_mesin', 'operator', 'temp', 'speed_depan', 'speed_belakang', 'speed', 'resep', 'density', 'jumlah_pcs', 'lebar_jadi', 'panjang_jadi', 'info_kualitas', 'gangguan_produksi'], 'boolean'],
            [['nama_proses', 'over_feed'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'max_pengulangan' => 'Max Pengulangan',
            'nama_proses' => 'Nama Proses',
            'tanggal' => 'Tanggal',
            'start' => 'Start',
            'stop' => 'Stop',
            'no_mesin' => 'No Mesin',
            'operator' => 'Operator',
            'temp' => 'Temp',
            'speed_depan' => 'Speed Depan',
            'speed_belakang' => 'Speed Belakang',
            'speed' => 'Speed',
            'resep' => 'Resep',
            'density' => 'Density',
            'jumlah_pcs' => 'Jumlah Pcs',
            'lebar_jadi' => 'Lebar Jadi',
            'panjang_jadi' => 'Panjang Jadi',
            'info_kualitas' => 'Info Kualitas',
            'gangguan_produksi' => 'Gangguan Produksi',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'over_feed' => 'Over Feed',
        ];
    }

    /**
     * Gets query for [[KartuProcessPrintingProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessPrintingProcesses()
    {
        return $this->hasMany(KartuProcessPrintingProcess::className(), ['process_id' => 'id']);
    }

    /**
     * Gets query for [[KartuProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcesses()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['id' => 'kartu_process_id'])->viaTable('kartu_process_printing_process', ['process_id' => 'id']);
    }
}
