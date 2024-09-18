<?php
namespace common\models\ar;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "trn_sc_agen".
 *
 * @property int $id
 * @property int $sc_id
 * @property string $date
 * @property string $nama_agen
 * @property string $attention
 * @property int|null $no_urut
 * @property string|null $no
 *
 * @property TrnSc $sc
 * @property TrnScKomisi[] $trnScKomisis
 */
class TrnScAgen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_agen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'date', 'nama_agen', 'attention'], 'required'],
            [['sc_id', 'no_urut'], 'default', 'value' => null],
            [['sc_id', 'no_urut'], 'integer'],
            [['date'], 'safe'],
            [['nama_agen', 'attention', 'no'], 'string', 'max' => 255],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::class, 'targetAttribute' => ['sc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'date' => 'Date',
            'nama_agen' => 'Nama Agen',
            'attention' => 'Attention',
            'no_urut' => 'No Urut',
            'no' => 'No',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::class, ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScKomisis()
    {
        return $this->hasMany(TrnScKomisi::class, ['sc_agen_id' => 'id']);
    }

    public function setNoUrut(){
        $this->no_urut = 1;

        $dateArr = explode('-', $this->date);
        $y = $dateArr[0];

        /* @var $lastData array*/
        $lastData = self::find()
            ->select(['id', 'no_urut'])
            ->where(['not', ['no_urut' => null]])
            ->andWhere(new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") = \''.$y.'\''))
            ->orderBy(['no_urut' => SORT_DESC])
            ->asArray()
            ->one()
        ;

        if(!empty($lastData)){
            $lastNoUrut = (int)$lastData['no_urut'];
            $this->no_urut = $lastNoUrut + 1;
        }
    }

    public function setNoLoa(){
        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $monthToAplhabet = '';
        switch ($month){
            case '01':
                $monthToAplhabet = 'A';
                break;
            case '02':
                $monthToAplhabet = 'B';
                break;
            case '03':
                $monthToAplhabet = 'C';
                break;
            case '04':
                $monthToAplhabet = 'D';
                break;
            case '05':
                $monthToAplhabet = 'E';
                break;
            case '06':
                $monthToAplhabet = 'F';
                break;
            case '07':
                $monthToAplhabet = 'G';
                break;
            case '08':
                $monthToAplhabet = 'H';
                break;
            case '09':
                $monthToAplhabet = 'I';
                break;
            case '10':
                $monthToAplhabet = 'J';
                break;
            case '11':
                $monthToAplhabet = 'K';
                break;
            case '12':
                $monthToAplhabet = 'L';
                break;
        }

        switch ($this->sc->tipe_kontrak){
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $tipeKontrakCode = 'L';
                break;
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $tipeKontrakCode = 'E';
                break;
            default:
                $tipeKontrakCode = '';
        }

        $this->no = $year.$monthToAplhabet.sprintf("%03s", $this->no_urut).$tipeKontrakCode;
    }
}
