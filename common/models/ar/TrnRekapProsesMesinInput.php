<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "trn_rekap_proses_mesin_input".
 *
 * @property int $id
 * @property int $mst_mesin_proses_id
 * @property string $tanggal
 * @property string $tipe
 * @property string $shift
 * @property string|null $wo_no
 * @property string|null $nk_no
 * @property string|null $nama_proses
 * @property string|null $temp
 * @property string|null $panjang_jadi
 * @property string|null $panjang_greige
 * @property string|null $keterangan
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstMesinProses $mstMesinProses
 */
class TrnRekapProsesMesinInput extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_rekap_proses_mesin_input';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mst_mesin_proses_id', 'tanggal', 'tipe', 'shift'], 'required'],
            [['mst_mesin_proses_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['mst_mesin_proses_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['tanggal'], 'safe'],
            [['keterangan'], 'string'],
            [['tipe', 'temp', 'panjang_jadi', 'panjang_greige'], 'string', 'max' => 50],
            [['shift'], 'string', 'max' => 10],
            [['wo_no', 'nk_no', 'nama_proses'], 'string', 'max' => 255],
            [['mst_mesin_proses_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstMesinProses::className(), 'targetAttribute' => ['mst_mesin_proses_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_mesin_proses_id' => 'Mesin Proses ID',
            'tanggal' => 'Tanggal',
            'tipe' => 'Tipe',
            'shift' => 'Shift',
            'wo_no' => 'WO',
            'nk_no' => 'NK',
            'nama_proses' => 'Nama Proses',
            'temp' => 'Temp',
            'panjang_jadi' => 'Panjang Jadi',
            'panjang_greige' => 'Panjang Greige',
            'keterangan' => 'Keterangan',
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
        return $this->hasOne(MstMesinProses::className(), ['id' => 'mst_mesin_proses_id']);
    }
}
