<?php

namespace common\models\ar;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "inspecting_dyeing_reject".
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
 * @property float|null $total_qty
 *
 * @property TrnKartuProsesDyeing $kartuProses
 * @property string $kartuProsesNo
 */
class InspectingDyeingReject extends \yii\db\ActiveRecord
{
    /**
     * ID item kartu proses (roll) yang dikembalikan ke processing
     * @var array
     */
    public $selected_items = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspecting_dyeing_reject';
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
            [['total_qty'], 'number'],
            [['date'], 'safe'],
            [['no', 'untuk_bagian', 'pcs', 'keterangan', 'penerima', 'mengetahui', 'pengirim'], 'string', 'max' => 255],
            [
                ['kartu_proses_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => TrnKartuProsesDyeing::className(),
                'targetAttribute' => ['kartu_proses_id' => 'id'],
                'filter' => [
                    'status' => [
                        TrnKartuProsesDyeing::STATUS_APPROVED,
                        TrnKartuProsesDyeing::STATUS_INSPECTED
                    ]
                ]
            ],
            ['selected_items', 'required', 'message' => 'Pilih minimal satu roll yang akan dikembalikan.'],
            ['selected_items', 'each', 'rule' => ['integer']],
            ['selected_items', 'validateSelectedItems', 'skipOnError' => true],
        ];
    }

    /**
     * Memastikan roll yang dipilih memang milik kartu proses ini
     * @param string $attribute
     */
    public function validateSelectedItems($attribute)
    {
        $validIds = TrnKartuProsesDyeingItem::find()
            ->select('id')
            ->where(['kartu_process_id' => $this->kartu_proses_id])
            ->column();

        if (array_diff($this->$attribute, $validIds)) {
            $this->addError($attribute, 'Roll yang dipilih tidak valid untuk kartu proses ini.');
        }
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
            'total_qty' => 'Total Qty',
            'kartuProsesNo' => 'Nomor Kartu Proses',
            'selected_items' => 'Roll Yang Dikembalikan',
        ];
    }

    /**
     * Gets query for [[KartuProses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_proses_id']);
    }

    /**
     * @return string
     */
    public function getKartuProsesNo()
    {
        return $this->kartuProses->no;
    }

    /**
     * Nomor kartu berikutnya untuk roll yang dikembalikan (misal "1/26" -> "1R1/26")
     * @param string $nomorKartu
     * @return string
     */
    public static function nextNomorKartuReject($nomorKartu)
    {
        $parts = explode('/', $nomorKartu, 2);
        $part2 = isset($parts[1]) ? '/' . $parts[1] : '';
        $seq = 1;
        do {
            $nomor = $parts[0] . 'R' . $seq . $part2;
            $seq++;
        } while (TrnKartuProsesDyeing::find()->where(['nomor_kartu' => $nomor])->exists());

        return $nomor;
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

        /* @var $lastData InspectingDyeingReject*/
        $lastData = InspectingDyeingReject::find()
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
