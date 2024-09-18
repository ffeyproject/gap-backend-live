<?php

namespace common\models\ar;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "nomor_surat_jalan".
 *
 * @property int $id
 * @property int $no_urut
 * @property string $no
 * @property string $date
 * @property int $created_at
 */
class NomorSuratJalan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nomor_surat_jalan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_urut', 'no', 'date', 'created_at'], 'required'],
            [['no_urut', 'created_at'], 'default', 'value' => null],
            [['no_urut', 'created_at'], 'integer'],
            [['date'], 'safe'],
            [['no'], 'string', 'max' => 255],
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
            'no' => 'No',
            'date' => 'Date',
            'created_at' => 'Created At',
        ];
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);

        //20/10/0002
        $this->no = $yearTwoDigit.'/'.$month.'/'.$noUrut;
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12

        /* @var $lastData JualExFinish*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$y.'-'.$m.'\''),
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") = \''.$y.'\''),
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
