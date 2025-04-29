<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\BaseVarDumper;

/**
 * This is the model class for table "trn_inspecting".
 *
 * @property int $id
 * @property int|null $sc_id
 * @property int|null $sc_greige_id
 * @property int|null $mo_id
 * @property int|null $wo_id
 * @property int|null $kartu_process_dyeing_id
 * @property int|null $memo_repair_id
 * @property int $jenis_process mereferensi ke TrnScGreige::jenisProsesOptions()
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string $tanggal_inspeksi
 * @property string|null $no_lot
 * @property string|null $kombinasi
 * @property string|null $note
 * @property int $status 1=draft, 2=posted, 3=approved, 4=delivered
 * @property int $unit 1=Meter, 2=Yard, 3=Kilogram
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property string|null $approval_reject_note
 * @property int|null $delivered_at
 * @property int|null $delivered_by
 * @property string|null $delivery_reject_note
 * @property int|null $kartu_process_printing_id
 *
 * @property TrnKartuProsesDyeing $kartuProcessDyeing
 * @property TrnMemoRepair $memoRepair
 * @property TrnKartuProsesPrinting $kartuProcessPrinting
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $approvedBy
 * @property User $deliveredBy
 * @property InspectingItem[] $inspectingItems
 *
 * @property string $woNo
 * @property string $kpdNo
 * @property string $kppNo
 * @property string $memoRepairNo
 */
class TrnInspecting extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_POSTED = 2;//langsung bypass jadi approved
    const STATUS_APPROVED = 3;//sudah bisa diterima oleh gudang jadi
    const STATUS_DELIVERED = 4;//Diterima oleh gudang jadi
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved', self::STATUS_DELIVERED => 'Delivered'];
    }

    const FRESH_INSPEKSI = 1;
    const RE_INSPEKSI = 2;

    public static function jenisInspeksiOptions(){
        return [self::FRESH_INSPEKSI => 'Fresh Order', self::RE_INSPEKSI => 'Re-Packing'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_inspecting';
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
            [['date', 'tanggal_inspeksi', 'jenis_process', 'jenis_inspek'], 'required'],

            [['date', 'tanggal_inspeksi'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED, self::STATUS_DELIVERED]],

            ['jenis_inspek', 'default', 'value'=>self::FRESH_INSPEKSI],
            ['jenis_inspek', 'required'],
            ['jenis_inspek'. 'in', 'range' => [self::FRESH_INSPEKSI, self::RE_INSPEKSI]],
            
            

            ['unit', 'default', 'value'=>MstGreigeGroup::UNIT_METER],
            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],
            

            ['jenis_process', 'default', 'value'=>TrnScGreige::PROCESS_DYEING],
            ['jenis_process', 'in', 'range' => [TrnScGreige::PROCESS_DYEING, TrnScGreige::PROCESS_PRINTING, TrnScGreige::PROCESS_PFP]],

            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_dyeing_id', 'memo_repair_id', 'jenis_process', 'no_urut', 'status', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'kartu_process_printing_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_dyeing_id', 'memo_repair_id', 'jenis_process', 'no_urut', 'status', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'kartu_process_printing_id'], 'integer'],

            [['no', 'no_lot', 'kombinasi'], 'string', 'max' => 255],
            [['kartu_process_dyeing_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesDyeing::className(), 'targetAttribute' => ['kartu_process_dyeing_id' => 'id']],
            [['memo_repair_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMemoRepair::className(), 'targetAttribute' => ['memo_repair_id' => 'id']],
            [['kartu_process_printing_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesPrinting::className(), 'targetAttribute' => ['kartu_process_printing_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['delivered_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['delivered_by' => 'id']],
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
            'kartu_process_dyeing_id' => 'Kartu Process Dyeing ID',
            'kartu_process_printing_id' => 'Kartu Process Printing ID',
            'memo_repair_id' => 'Memo Repair ID',
            'jenis_process' => 'Jenis Process',
            'jenis_inspek' => 'Jenis Inspeksi',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Tanggal Kirim',
            'tanggal_inspeksi' => 'Tanggal Inspeksi',
            'no_lot' => 'No Lot',
            'kombinasi' => 'Kombinasi',
            'note' => 'Note',
            'status' => 'Status',
            'unit' => 'Unit',
            'jenis_inspek' => 'Jenis Inspeksi',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'approval_reject_note' => 'Approval Reject Note',
            'delivered_at' => 'Delivered At',
            'delivered_by' => 'Delivered By',
            'delivery_reject_note' => 'Delivery Reject Note',
            'jenis_inspek' => 'Jenis Inspek',
            'woNo' => 'No. WO',
            'kpdNo' => 'No. KP. Dyeing',
            'kppNo' => 'No. KP. Printing',
            'memoRepairNo' => 'No. Memo Repair',
        ];
    }

    /**
     * Gets query for [[KartuProcessMaklon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemoRepair()
    {
        return $this->hasOne(TrnMemoRepair::className(), ['id' => 'memo_repair_id']);
    }

    /**
     * @return string
     */
    public function getMemoRepairNo()
    {
        return $this->memoRepair->no;
    }

    /**
     * Gets query for [[KartuProcessPrinting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessPrinting()
    {
        return $this->hasOne(TrnKartuProsesPrinting::className(), ['id' => 'kartu_process_printing_id']);
    }

    /**
     * @return string
     */
    public function getKppNo()
    {
        return $this->kartuProcessPrinting->no;
    }

    /**
     * Gets query for [[KartuProcessDyeing]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessDyeing()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_process_dyeing_id']);
    }

    /**
     * @return string
     */
    public function getKpdNo()
    {
        return $this->kartuProcessDyeing->no;
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
     * @return string
     */
    public function getWoNo()
    {
        return $this->wo->no;
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * Gets query for [[DeliveredBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveredBy()
    {
        return $this->hasOne(User::className(), ['id' => 'delivered_by']);
    }

    /**
     * Gets query for [[InspectingItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectingItems()
    {
        return $this->hasMany(InspectingItem::className(), ['inspecting_id' => 'id']);
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

        /* @var $lastData TrnInspecting*/
        $lastData = TrnInspecting::find()
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