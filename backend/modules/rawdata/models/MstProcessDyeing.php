<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_process_dyeing".
 *
 * @property int $id
 * @property int $order nomor urutan proses
 * @property int $max_pengulangan
 * @property string $nama_proses
 * @property bool $tanggal
 * @property bool $start
 * @property bool $stop
 * @property bool $no_mesin
 * @property bool $shift_group
 * @property bool $temp
 * @property bool $speed
 * @property bool $gramasi
 * @property bool $program_number
 * @property bool $density
 * @property bool $over_feed
 * @property bool $lebar_jadi
 * @property bool $panjang_jadi
 * @property bool $info_kualitas
 * @property bool $gangguan_produksi
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property KartuProcessCelupProcess[] $kartuProcessCelupProcesses
 * @property TrnKartuProsesCelup[] $kartuProcesses
 * @property KartuProcessDyeingProcess[] $kartuProcessDyeingProcesses
 * @property TrnKartuProsesDyeing[] $kartuProcesses0
 */
class MstProcessDyeing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_process_dyeing';
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
            [['tanggal', 'start', 'stop', 'no_mesin', 'shift_group', 'temp', 'speed', 'gramasi', 'program_number', 'density', 'over_feed', 'lebar_jadi', 'panjang_jadi', 'info_kualitas', 'gangguan_produksi'], 'boolean'],
            [['nama_proses'], 'string', 'max' => 255],
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
            'shift_group' => 'Shift Group',
            'temp' => 'Temp',
            'speed' => 'Speed',
            'gramasi' => 'Gramasi',
            'program_number' => 'Program Number',
            'density' => 'Density',
            'over_feed' => 'Over Feed',
            'lebar_jadi' => 'Lebar Jadi',
            'panjang_jadi' => 'Panjang Jadi',
            'info_kualitas' => 'Info Kualitas',
            'gangguan_produksi' => 'Gangguan Produksi',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[KartuProcessCelupProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessCelupProcesses()
    {
        return $this->hasMany(KartuProcessCelupProcess::className(), ['process_id' => 'id']);
    }

    /**
     * Gets query for [[KartuProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcesses()
    {
        return $this->hasMany(TrnKartuProsesCelup::className(), ['id' => 'kartu_process_id'])->viaTable('kartu_process_celup_process', ['process_id' => 'id']);
    }

    /**
     * Gets query for [[KartuProcessDyeingProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessDyeingProcesses()
    {
        return $this->hasMany(KartuProcessDyeingProcess::className(), ['process_id' => 'id']);
    }

    /**
     * Gets query for [[KartuProcesses0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcesses0()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_id'])->viaTable('kartu_process_dyeing_process', ['process_id' => 'id']);
    }
}
