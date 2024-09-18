<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "trn_sc_greige".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $greige_group_id
 * @property int $process 1=DYEING, 2=PRINTING, 3=PFP
 * @property int $lebar_kain 1=44, 2=58, 3=64
 * @property string $merek
 * @property int $grade 1=A, 2=B, 3=C, 4=ALL GRADE
 * @property string $piece_length
 * @property float $unit_price
 * @property string $price_param 1=Per Unit, 2=Per Yard, 3=Pek Kilogram
 * @property float $qty
 * @property string|null $woven_selvedge
 * @property string|null $note
 * @property bool|null $closed
 * @property string|null $closing_note
 * @property string|null $no_order_greige
 * @property int|null $no_urut_order_greige
 * @property string|null $order_greige_note
 * @property bool $order_grege_approved
 * @property int|null $order_grege_approved_at
 * @property int|null $order_grege_approved_by
 * @property string|null $order_grege_approval_note
 * @property bool $order_grege_approved_dir
 * @property int|null $order_grege_approved_at_dir
 * @property string|null $order_grege_approval_note_dir
 *
 * @property User $kabagPmc
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnMo[] $trnMos
 * @property TrnMo[] $trnMosAktif
 * @property TrnMoColor[] $trnMoColors
 * @property TrnMoColor[] $trnMoColorsAktif
 * @property TrnMoColor[] $trnMoColorsBatal
 * @property MstGreigeGroup $greigeGroup
 * @property TrnSc $sc
 * @property TrnScKomisi[] $trnScKomisis
 * @property TrnWo[] $trnWos
 * @property TrnWoColor[] $trnWoColors
 * @property TrnKirimBuyer[] $trnKirimBuyerPosted
 *
 * @property string $lebarKainName
 *
 * @property float $qtyBatchToUnit
 * @property float $qtyBatchToMeter
 * @property float $qtyBatchToYard
 * @property float $qtyFinish
 * @property float $qtyFinishToMeter
 * @property float $qtyFinishToYard
 * @property float $totalPrice
 * @property float $qtyTrnKirimBuyerPosted
 * @property float $sisaMoBatch
 * @property float $sisaWoBatch
 */
class TrnScGreige extends \yii\db\ActiveRecord
{
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS
    const PROCESS_DYEING = 1;const PROCESS_PRINTING = 2;const PROCESS_PFP = 3; const PROCESS_DIGITAL_PRINTING = 4;
    /**
     * @return array
     */
    public static function processOptions(){
        return [
            self::PROCESS_DYEING => 'Dyeing',
            self::PROCESS_PRINTING => 'Printing',
	    self::PROCESS_DIGITAL_PRINTING => 'Digital Printing',
            self::PROCESS_PFP => 'PFP',
        ];
    }

    const LEBAR_KAIN_44 = 1;const LEBAR_KAIN_58 = 2;const LEBAR_KAIN_64 = 3;const LEBAR_KAIN_66 = 4;
    /**
     * @return array
     */
    public static function lebarKainOptions(){
        return [
            self::LEBAR_KAIN_44 => '44',
            self::LEBAR_KAIN_58 => '58',
            self::LEBAR_KAIN_64 => '64',
            self::LEBAR_KAIN_66 => '66',
        ];
    }

    const GRADE_A = 1;const GRADE_B = 2;const GRADE_C = 3;const GRADE_ALL = 4;
    /**
     * @return array
     */
    public static function gradeOptions(){
        return [
            self::GRADE_A => 'A',
            self::GRADE_B => 'B',
            self::GRADE_C => 'C',
            self::GRADE_ALL => 'All Grade',
        ];
    }

    const PRICE_PARAM_PER_METER = 1; const PRICE_PARAM_PER_YARD = 2; const PRICE_PARAM_PER_KILOGRAM = 3;
    /**
     * @return array
     */
    public static function priceParamOptions(){
        return [
            self::PRICE_PARAM_PER_METER => 'Per Meter',
            self::PRICE_PARAM_PER_YARD => 'Per Yard',
            self::PRICE_PARAM_PER_KILOGRAM => 'Per Kilogram',
        ];
    }
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_greige';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'greige_group_id', 'process', 'lebar_kain', 'merek', 'grade', 'piece_length', 'unit_price', 'price_param', 'qty'], 'required'],
            [['sc_id', 'greige_group_id', 'grade', 'no_urut_order_greige'], 'default', 'value' => null],
            [['sc_id', 'greige_group_id', 'process', 'lebar_kain', 'grade', 'no_urut_order_greige', 'order_grege_approved_at', 'order_grege_approved_by', 'order_grege_approved_at_dir'], 'integer'],
            [['unit_price', 'qty'], 'number'],
            [['woven_selvedge', 'note', 'closing_note', 'order_greige_note', 'order_grege_approval_note', 'order_grege_approval_note_dir'], 'string'],
            [['closed', 'order_grege_approved', 'order_grege_approved_dir'], 'boolean'],
            [['merek', 'no_order_greige'], 'string', 'max' => 255],
            [['piece_length', 'price_param'], 'string', 'max' => 100],

            ['process', 'default', 'value'=>TrnScGreige::PROCESS_DYEING],
            ['process', 'in', 'range' => [TrnScGreige::PROCESS_DYEING, TrnScGreige::PROCESS_PRINTING, TrnScGreige::PROCESS_PFP]],

            ['lebar_kain', 'default', 'value'=>TrnScGreige::LEBAR_KAIN_44],
            ['lebar_kain', 'in', 'range' => [TrnScGreige::LEBAR_KAIN_44, TrnScGreige::LEBAR_KAIN_58, TrnScGreige::LEBAR_KAIN_64, TrnScGreige::LEBAR_KAIN_66]],

            ['grade', 'default', 'value'=>TrnScGreige::GRADE_A],
            ['grade', 'in', 'range' => [TrnScGreige::GRADE_A, TrnScGreige::GRADE_B, TrnScGreige::GRADE_C, TrnScGreige::GRADE_ALL]],

            ['price_param', 'default', 'value'=>TrnScGreige::PRICE_PARAM_PER_METER],
            ['price_param', 'in', 'range' => [TrnScGreige::PRICE_PARAM_PER_METER, TrnScGreige::PRICE_PARAM_PER_YARD, TrnScGreige::PRICE_PARAM_PER_KILOGRAM]],

            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
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
            'greige_group_id' => 'Greige Group ID',
            'process' => 'Process',
            'lebar_kain' => 'Lebar Kain',
            'merek' => 'Merek',
            'grade' => 'Grade',
            'piece_length' => 'Piece Length',
            'unit_price' => 'Unit Price',
            'price_param' => 'Price Param',
            'qty' => 'Qty (Batch)',
            'woven_selvedge' => 'Woven Selvedge',
            'note' => 'Note',
            'closed' => 'Closed',
            'closing_note' => 'Closing Note',
            'no_order_greige' => 'No Order Greige',
            'no_urut_order_greige' => 'No Urut Order Greige',
            'order_greige_note' => 'Order Greige Note',
            'order_grege_approved' => 'Order Greige Sudah Disetujui PMC',
            'order_grege_approved_at' => 'Order Greige Disetujui PMC Pada',
            'order_grege_approved_by' => 'Order Greige Disetujui PMC Oleh',
            'order_grege_approval_note' => 'Catatan Persetujuan PMC',
            'order_grege_approved_dir' => 'Order Greige Sudah Disetujui DIR',
            'order_grege_approved_at_dir' => 'Order Greige Disetujui DIR Pada',
            'order_grege_approval_note_dir' => 'Catatan Persetujuan DIR',
            'lebarKainName' => 'Lebar Kain'
        ];
    }

    /**
     * @return User|null
     */
    public function getKabagPmc()
    {
        if ($this->order_grege_approved_by !== null){
            return User::findOne($this->order_grege_approved_by);
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return TrnKirimBuyer[]
     */
    public function getTrnKirimBuyerPosted()
    {
        return TrnKirimBuyer::find()
            ->joinWith('header')
            ->where(['trn_kirim_buyer.sc_greige_id'=>$this->id])
            ->andWhere(['trn_kirim_buyer_header.status'=>TrnKirimBuyerHeader::STATUS_POSTED])
            ->all()
            ;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * mo yang masuk perhitungan statistik
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMosAktif()
    {
        return $this->getTrnMos()->where(['<>', 'trn_mo.status', TrnMo::STATUS_BATAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColors()
    {
        return $this->hasMany(TrnMoColor::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColorsAktif()
    {
        return $this->getTrnMoColors()->joinWith('mo')->where(['<>', 'trn_mo.status', TrnMo::STATUS_BATAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColorsBatal()
    {
        return $this->getTrnMoColors()->joinWith('mo')->where(['trn_mo.status'=>TrnMo::STATUS_BATAL]);
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
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScKomisis()
    {
        return $this->hasMany(TrnScKomisi::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getLebarKainName()
    {
        return self::lebarKainOptions()[$this->lebar_kain];
    }

    /**
     * @return float
     */
    public function getQtyBatchToUnit(){
        return $this->qty * $this->greigeGroup->qty_per_batch;
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyBatchToMeter(){
        switch ($this->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->getQtyBatchToUnit());
                break;
            case MstGreigeGroup::UNIT_METER:
                return $this->getQtyBatchToUnit();
                break;
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
                break;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->greigeGroup->unit].' belum didukung');
        }
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyBatchToYard(){
        switch ($this->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return $this->getQtyBatchToUnit();
                break;
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->getQtyBatchToUnit());
                break;
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
                break;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->greigeGroup->unit].' belum didukung');
        }
    }

    /**
     * @return float
     */
    public function getQtyFinish(){
        return floatval($this->qty) * $this->greigeGroup->qtyFinish;
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyFinishToYard(){
        switch ($this->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->qtyFinish);
                break;
            case MstGreigeGroup::UNIT_YARD:
                return $this->qtyFinish;
                break;
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
                break;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->greigeGroup->unit].' belum didukung');
        }
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyFinishToMeter(){
        switch ($this->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return $this->qtyFinish;
                break;
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->qtyFinish);
                break;
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
                break;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->greigeGroup->unit].' belum didukung');
        }
    }

    /**
     * @return float
     */
    public function getTotalPrice(){
        if($this->price_param == self::PRICE_PARAM_PER_YARD){
            if($this->greigeGroup->unit == MstGreigeGroup::UNIT_KILOGRAM){
                return 0;
            }
            return floatval($this->unit_price) * $this->qtyFinishToYard;
        }

        return floatval($this->unit_price) * $this->qtyFinish;
    }

    /**
     * @return float
     */
    public function getSisaMoBatch(){
        $mosQty = $this->getTrnMoColorsAktif()->sum('qty');
        $mosQty = $mosQty > 0 ? $mosQty : 0;
        return $this->qty - $mosQty;
    }

    /**
     * @return float
     */
    public function getSisaWoBatch(){
        $wosQty = TrnWoColor::find()->joinWith('wo')
            ->where([
                'and',
                ['trn_wo_color.sc_greige_id'=>$this->id],
                ['<>', 'trn_wo.status', TrnWo::STATUS_BATAL]
            ])->sum('qty')
        ;
        $mosQty = $wosQty > 0 ? $wosQty : 0;
        return $this->qty - $mosQty;
    }

    /**
     * @param $date
     * @param $tipeKontrak
     * @param $noUrut
     * @param $jenisOrderCode
     * @param $noUrutSc
     */
    public function setNoOrderGreige($date, $tipeKontrak, $noUrut, $jenisOrder, $noUrutSc){
        switch ($tipeKontrak){
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $tipeKontrakCode = 'L';
                break;
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $tipeKontrakCode = 'E';
                break;
            default:
                $tipeKontrakCode = '-';
        }

        $this->no_urut_order_greige = $noUrut;

        $dateArr = explode('-', $date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        switch ($jenisOrder){
            case TrnSc::JENIS_ORDER_FRESH_ORDER:
                $jenisOrderCode = 'FO';
                break;
            case TrnSc::JENIS_ORDER_MAKLOON:
                $jenisOrderCode = 'MA';
                break;
            case TrnSc::JENIS_ORDER_BARANG_JADI:
                $jenisOrderCode = 'BJ';
                break;
            case TrnSc::JENIS_ORDER_STOK:
                $jenisOrderCode = 'ST';
                break;
            default:
                $jenisOrderCode = '-';
        }

        $this->no_order_greige = $jenisOrderCode.sprintf("%04s", $noUrutSc).$tipeKontrakCode.'/G/'.$noUrut.'/'.$month.'/'.$year;
    }
}
