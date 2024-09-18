<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_order_pfp".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property float $qty
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted, 3=Approved, 4=Processed
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $handling_id
 *
 * @property int $approved_by
 * @property int|null $approved_at
 * @property string|null $approval_note
 * @property int|null $proses_sampai 1=Sampai Preset, 2=Sampai Setting
 * @property string|null $dasar_warna
 *
 * @property TrnKartuProsesPfp[] $trnKartuProsesPfps
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property MstGreige $greige
 * @property MstHandling $handling
 * @property MstGreigeGroup $greigeGroup
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $approvedBy
 */
class TrnOrderPfp extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2; const STATUS_APPROVED = 3; const STATUS_PROCESSED = 4; const STATUS_BATAL = 5;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_BATAL => 'Batal',
        ];
    }

    const PROSES_SAMPAI_PRESET = 1;const PROSES_SAMPAI_SETTING = 2;
    /**
     * @return array
     */
    public static function prosesSampaiOptions(){
        return [
            self::PROSES_SAMPAI_PRESET => 'Sampai Preset',
            self::PROSES_SAMPAI_SETTING => 'Sampai Setting',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_order_pfp';
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
            [['greige_id', 'handling_id', 'qty', 'date', 'approved_by'], 'required'],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'approved_by', 'approved_at', 'proses_sampai','batal_at','batal_by','jenis_gudang'], 'integer'],
            [['qty'], 'number'],
            [['note', 'approval_note'], 'string'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_PROCESSED, self::STATUS_BATAL]],

            ['proses_sampai', 'default', 'value'=>null],
            ['proses_sampai', 'in', 'range' => [self::PROSES_SAMPAI_PRESET, self::PROSES_SAMPAI_SETTING]],

            [['no', 'dasar_warna'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],

            ['jenis_gudang', 'default', 'value'=>TrnStockGreige::JG_FRESH],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'no_urut' => 'Nomor Urut',
            'no' => 'Nomor',
            'qty' => 'Qty',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Tanggal',
            'created_at' => 'Dibuat Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_at' => 'Diubah Pada',
            'updated_by' => 'Diubah Oleh',
            'handling_id' => 'Handling ID',
            'approved_by' => 'Disetujui Oleh',
            'approved_at' => 'Disetujui Pada',
            'approval_note' => 'Catatan Penolakan Persetujuan',
            'proses_sampai' => 'Proses Sampai',
            'dasar_warna' => 'Dasar Warna',
            'batal_at' => 'Dibatalkan Pada',
            'batal_by' => 'Dibatalkan Oleh',
            'batal_note' => 'Catatan Penolakan Batalkan',
            'validasi_stock' => 'Habiskan Stock',
            'jenis_gudang' => 'Jenis Gudang',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfps()
    {
        return $this->hasMany(TrnKartuProsesPfp::className(), ['order_pfp_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['order_pfp_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[Handling0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandling()
    {
        return $this->hasOne(MstHandling::className(), ['id' => 'handling_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRejectBy()
    {
        return $this->hasOne(User::className(), ['id' => 'batal_by']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);

        //OPFP/2103/00001
        $this->no = "OPFP/{$yearTwoDigit}{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnOrderPfp*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''), //reset tiap bulan
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") = \''.$y.'\''), //reset tiap taun
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQtyBatchToUnit()
    {   
        $gap = $this->greige->gap;
        return $this->qty * ($this->greigeGroup->qty_per_batch + $gap);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQtyBatchToMeter()
    {   
        switch ($this->greige->group->unit){
            case MstGreigeGroup::UNIT_YARD:
                return $this->greige->group->unit;
                return Converter::yardToMeter($this->qtyBatchToUnit);
            case MstGreigeGroup::UNIT_METER:
                return $this->qtyBatchToUnit;
            default:
                return 0;
        }
    }
}
