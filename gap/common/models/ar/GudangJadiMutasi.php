<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "gudang_jadi_mutasi".
 *
 * @property int $id
 * @property int|null $no_urut
 * @property string|null $nomor
 * @property string $date
 * @property string $pengirim
 * @property string $penerima
 * @property string $kepala_gudang
 * @property string $dept_tujuan
 * @property string $note
 * @property int $status 1=Draft, 2=Posted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property GudangJadiMutasiItem[] $gudangJadiMutasiItems
 *
 * @property string $statusName
 */
class GudangJadiMutasi extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gudang_jadi_mutasi';
    }

    /**
     * {@inheritdoc}
     */
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
            [['date', 'pengirim', 'penerima', 'kepala_gudang', 'dept_tujuan'], 'required'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['note'], 'string'],
            [['nomor', 'pengirim', 'penerima', 'kepala_gudang', 'dept_tujuan'], 'string', 'max' => 255],
            [['no_urut', 'nomor'], 'required', 'on'=>['ubah_nomor']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_urut' => 'No Urut',
            'nomor' => 'Nomor',
            'date' => 'Tanggal',
            'pengirim' => 'Pengirim',
            'penerima' => 'Penerima',
            'kepala_gudang' => 'Kepala Gudang',
            'dept_tujuan' => 'Dept Tujuan',
            'note' => 'Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',

            'statusName' => 'Status'
        ];
    }

    /**
     * Gets query for [[GudangJadiMutasiItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadiMutasiItems()
    {
        return $this->hasMany(GudangJadiMutasiItem::className(), ['mutasi_id' => 'id']);
    }

    /**
     * @return string
    */
    public function getStatusName(){
        return self::statusOptions()[$this->status];
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->nomor = "{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData GudangJadiMutasi*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->asArray()
            ->one();

        if($lastData !== null){
            $this->no_urut = (int)$lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
