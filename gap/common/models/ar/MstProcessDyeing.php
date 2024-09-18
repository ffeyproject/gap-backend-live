<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mst_process_dyeing".
 *
 * @property int $id
 * @property int $order nomor urutan proses
 * @property int $max_pengulangan
 * @property string $nama_proses
 * @property bool|null $tanggal
 * @property bool|null $start
 * @property bool|null $stop
 * @property bool|null $no_mesin
 * @property bool|null $shift_group
 * @property bool|null $temp
 * @property bool|null $speed
 * @property bool|null $gramasi
 * @property bool|null $program_number
 * @property bool|null $density
 * @property bool|null $over_feed
 * @property bool|null $lebar_jadi
 * @property bool|null $panjang_jadi
 * @property bool|null $info_kualitas
 * @property bool|null $gangguan_produksi
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property KartuProcessDyeingProcess[] $kartuProcessDyeingProcesses
 * @property TrnKartuProsesDyeing[] $kartuProcesses
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
            ['order', 'number'],
            [['order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
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
            'order' => 'Order No',
            'max_pengulangan' => 'Maksimal Pengulangan',
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
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'max_pengulangan' => 'Maksimal jumlah pengulangan yang diperbolehkan.',
            'order' => 'Urutan proses.',

        ];
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
     * Gets query for [[KartuProcesses]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getKartuProcesses()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_id'])->viaTable('kartu_process_dyeing_process', ['process_id' => 'id']);
    }
}
