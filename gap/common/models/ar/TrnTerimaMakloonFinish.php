<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_terima_makloon_finish".
 *
 * @property int $id
 * @property int|null $jenis_gudang Mereferensi ke TrnGudangJadi::jenisGudangOptions()
 * @property int|null $sc_id
 * @property int|null $sc_greige_id
 * @property int|null $mo_id
 * @property int|null $wo_id
 * @property int $kirim_makloon_id
 * @property int|null $vendor_id
 * @property string $date
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted, 3=Inspected
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $pengirim
 * @property int $wo_color_id
 *
 * @property TrnWoColor $woColor
 * @property MstVendor $vendor
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnKirimMakloon $kirimMakloon
 * @property TrnWo $wo
 * @property TrnTerimaMakloonFinishItem[] $trnTerimaMakloonFinishItems
 */
class TrnTerimaMakloonFinish extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2; const STATUS_INSPECTED = 3;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_INSPECTED => 'Inspected'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_terima_makloon_finish';
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
            [['jenis_gudang', 'kirim_makloon_id', 'date', 'wo_color_id'], 'required'],

            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kirim_makloon_id', 'wo_color_id', 'vendor_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_INSPECTED]],

            ['unit', 'default', 'value'=>MstGreigeGroup::UNIT_METER],
            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            ['jenis_gudang', 'default', 'value'=>TrnGudangJadi::JENIS_GUDANG_LOKAL],
            ['jenis_gudang', 'in', 'range' => [TrnGudangJadi::JENIS_GUDANG_LOKAL, TrnGudangJadi::JENIS_GUDANG_EXPORT, TrnGudangJadi::JENIS_GUDANG_GRADE_B]],

            [['note'], 'string'],
            [['no', 'pengirim'], 'string', 'max' => 255],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstVendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['kirim_makloon_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimMakloon::className(), 'targetAttribute' => ['kirim_makloon_id' => 'id']],
            [['wo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWoColor::className(), 'targetAttribute' => ['wo_color_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_gudang' => 'Jenis Gudang',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'kirim_makloon_id' => 'Kirim Makloon ID',
            'vendor_id' => 'Vendor ID',
            'date' => 'Date',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'unit' => 'Unit',
            'note' => 'Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'pengirim' => 'Pengirim',
            'wo_color_id' => 'WO Color Id',
        ];
    }

    /**
     * Gets query for [[Vendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(MstVendor::className(), ['id' => 'vendor_id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWoColor()
    {
        return $this->hasOne(TrnWoColor::className(), ['id' => 'wo_color_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKirimMakloon()
    {
        return $this->hasOne(TrnKirimMakloon::className(), ['id' => 'kirim_makloon_id']);
    }

    /**
     * Gets query for [[TrnTerimaMakloonFinishItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnTerimaMakloonFinishItems()
    {
        return $this->hasMany(TrnTerimaMakloonFinishItem::className(), ['terima_makloon_id' => 'id']);
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

        /* @var $lastData TrnKirimMakloon*/
        $lastData = self::find()
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
