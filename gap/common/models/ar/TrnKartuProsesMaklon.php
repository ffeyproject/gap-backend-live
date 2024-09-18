<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_kartu_proses_maklon".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int $vendor_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property int $status 1=draft, 2=posted, 3=approved (disetujui untuk dikirim ke vendor), 4=Batal
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 *
 * @property TrnInspecting[] $trnInspectings
 * @property MstVendor $vendor
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
 */
class TrnKartuProsesMaklon extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;const STATUS_BATAL = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Disetujui', self::STATUS_BATAL => 'Batal'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_maklon';
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
            [['wo_id', 'vendor_id', 'date', 'unit'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['note'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED, self::STATUS_BATAL]],

            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            [['no'], 'string', 'max' => 255],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstVendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'vendor_id' => 'Vendor ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'note' => 'Note',
            'date' => 'Date',
            'posted_at' => 'Posted At',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'unit' => 'Unit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['kartu_process_maklon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(MstVendor::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['kartu_process_id' => 'id']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->wo->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesMaklon*/
        $lastData = self::find()
            ->select('trn_kartu_proses_maklon.id, trn_kartu_proses_maklon.no_urut, trn_kartu_proses_maklon.date')
            ->joinWith('wo', false)
            ->where([
                'and',
                ['not', ['trn_kartu_proses_maklon.no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
                ['trn_wo.greige_id'=>$this->wo->greige_id]
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
