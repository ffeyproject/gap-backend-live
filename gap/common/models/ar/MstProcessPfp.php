<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mst_process_pfp".
 *
 * @property int $id
 * @property int $order nomor urutan proses
 * @property int $max_pengulangan
 * @property string $nama_proses
 * @property bool|null $tanggal
 * @property bool|null $start
 * @property bool|null $stop
 * @property bool|null $no_mesin
 * @property bool|null $shift_operator
 * @property bool|null $temp
 * @property bool|null $speed
 * @property bool|null $waktu
 * @property bool|null $program_number
 * @property bool|null $ex_relax
 * @property bool|null $ex_wr_oligomer
 * @property bool|null $ex_dyeing
 * @property bool|null $wr_pcnt
 * @property bool|null $rpm
 * @property bool|null $density
 * @property bool|null $jamur
 * @property bool|null $karat
 * @property bool|null $over_feed
 * @property bool|null $counter
 * @property bool|null $lebar_jadi
 * @property bool|null $info_kualitas
 * @property bool|null $gangguan_produksi
 * @property bool|null $gramasi
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property KartuProcessPfpProcess[] $kartuProcessPfpProcesses
 * @property TrnKartuProsesPfp[] $kartuProcesses
 */
class MstProcessPfp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_process_pfp';
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
            [['order', 'nama_proses'], 'required'],
            [['order', 'max_pengulangan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['order', 'max_pengulangan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['tanggal', 'start', 'stop', 'no_mesin', 'shift_operator', 'temp', 'speed', 'waktu', 'program_number', 'ex_relax', 'ex_wr_oligomer', 'ex_dyeing', 'wr_pcnt', 'rpm', 'density', 'jamur', 'karat', 'over_feed', 'counter', 'lebar_jadi', 'info_kualitas', 'gangguan_produksi', 'gramasi'], 'boolean'],
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
            'shift_operator' => 'Shift Operator',
            'temp' => 'Temp',
            'speed' => 'Speed',
            'waktu' => 'Waktu',
            'program_number' => 'Program Number',
            'ex_relax' => 'Ex Relax',
            'ex_wr_oligomer' => 'Ex Wr Oligomer',
            'ex_dyeing' => 'Ex Dyeing',
            'wr_pcnt' => 'Wr Pcnt',
            'rpm' => 'Rpm',
            'density' => 'Density',
            'jamur' => 'Jamur',
            'karat' => 'Karat',
            'over_feed' => 'Over Feed',
            'counter' => 'Counter',
            'lebar_jadi' => 'Lebar Jadi',
            'info_kualitas' => 'Info Kualitas',
            'gangguan_produksi' => 'Gangguan Produksi',
            'gramasi' => 'Gramasi',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[KartuProcessPfpProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessPfpProcesses()
    {
        return $this->hasMany(KartuProcessPfpProcess::className(), ['process_id' => 'id']);
    }

    /**
     * Gets query for [[KartuProcesses]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getKartuProcesses()
    {
        return $this->hasMany(TrnKartuProsesPfp::className(), ['id' => 'kartu_process_id'])->viaTable('kartu_process_pfp_process', ['process_id' => 'id']);
    }
}
