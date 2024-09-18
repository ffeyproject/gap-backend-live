<?php

namespace common\models\ar;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "inspecting_printing_reject".
 *
 * @property int $id
 * @property int $kartu_proses_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $untuk_bagian
 * @property string|null $pcs
 * @property string|null $keterangan
 * @property string|null $penerima
 * @property string|null $mengetahui
 * @property string|null $pengirim
 * @property int $created_at
 * @property int $created_by
 *
 * @property TrnKartuProsesPrinting $kartuProses
 * @property string $kartuProsesNo
 */
class InspectingPrintingReject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspecting_printing_reject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_proses_id', 'date', 'untuk_bagian', 'pcs', 'keterangan', 'penerima', 'mengetahui', 'pengirim'], 'required'],
            [['kartu_proses_id', 'no_urut', 'created_at', 'created_by'], 'default', 'value' => null],
            [['kartu_proses_id', 'no_urut', 'created_at', 'created_by'], 'integer'],
            [['date'], 'safe'],
            [['no', 'untuk_bagian', 'pcs', 'keterangan', 'penerima', 'mengetahui', 'pengirim'], 'string', 'max' => 255],
            [
                ['kartu_proses_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => TrnKartuProsesPrinting::className(),
                'targetAttribute' => ['kartu_proses_id' => 'id'],
                'filter' => ['status' => TrnKartuProsesPrinting::STATUS_APPROVED]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kartu_proses_id' => 'Kartu Proses ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'untuk_bagian' => 'Untuk Bagian',
            'pcs' => 'Pcs',
            'keterangan' => 'Keterangan',
            'penerima' => 'Penerima',
            'mengetahui' => 'Mengetahui',
            'pengirim' => 'Pengirim',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'kartuProsesNo' => 'Nomor Kartu Proses',
        ];
    }

    /**
     * Gets query for [[KartuProses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesPrinting::className(), ['id' => 'kartu_proses_id']);
    }

    /**
     * @return string
     */
    public function getKartuProsesNo()
    {
        return $this->kartuProses->no;
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData InspectingPrintingReject*/
        $lastData = InspectingPrintingReject::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
