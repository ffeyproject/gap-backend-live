<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "trn_mo".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $process mengacu ke field process pada table sc_greige
 * @property int|null $approval_id
 * @property int|null $approved_at
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $re_wo nomor WO referensi
 * @property string|null $design
 * @property string|null $article
 * @property string|null $strike_off
 * @property bool|null $heat_cut
 * @property string|null $sulam_pinggir
 * @property int|null $border_size
 * @property int|null $block_size
 * @property bool|null $foil
 * @property string|null $face_stamping
 * @property string|null $selvedge_stamping
 * @property string|null $selvedge_continues
 * @property string|null $side_band
 * @property string|null $tag
 * @property string|null $hanger
 * @property string|null $label
 * @property string|null $folder
 * @property string|null $album
 * @property bool|null $joint
 * @property int|null $joint_qty
 * @property int $packing_method 1=SINGLE ROLL, 2=DOUBLE FOLDED
 * @property int $shipping_method 1=BALE, 2=CARTOON, 3=LOSE
 * @property int $shipping_sorting 1=SOLID, 2=ASSORTED
 * @property int $plastic 1=VACUM, 2=NON VACUM
 * @property string|null $arsip
 * @property bool|null $jet_black
 * @property string|null $piece_length
 * @property string $est_produksi
 * @property string $est_packing
 * @property string $target_shipment
 * @property int|null $posted_at
 * @property int|null $closed_at
 * @property int|null $closed_by
 * @property string|null $closed_note
 * @property string|null $reject_notes
 * @property int|null $batal_at
 * @property int|null $batal_by
 * @property string|null $batal_note
 * @property int $status 1=draft, 2=posted, 3=approved, 4=rejected, 5=closed, 6=batal
 * @property string|null $note
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $jenis_gudang mereferensi ke TrnStockGreige::jenisGudangOptions()
 * @property string|null $handling
 * @property string|null $no_lab_dip
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property User $approval
 * @property User $closedBy
 * @property User $batalBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnMoColor[] $trnMoColors
 * @property TrnMoMemo[] $trnMoMemos
 * @property TrnWo[] $trnWos
 * @property TrnWo[] $trnWosAktif
 * @property TrnWo[] $trnWosDraft
 * @property TrnWo[] $trnWosOk
 * @property TrnWoColor[] $trnWoColors
 * @property TrnWoColor[] $trnWoColorsAktif
 * @property TrnWoColor[] $trnWoColorsDraft
 * @property TrnWoColor[] $trnWoColorsOk
 * @property TrnWoColor[] $trnWoColorsBatal
 * @property float $trnWoColorsAktifQty
 * @property float $trnWoColorsDraftQty
 * @property float $trnWoColorsOkQty
 * @property float $trnWoColorsBatalQty
 *
 * @property string $approvalName
 * @property string $creatorName
 * @property string $updatorName
 * @property string $approvalStatus
 * @property string $marketingName
 *
 * @property float $colorQty
 * @property float $colorQtyBatchToUnit
 * @property float $colorQtyFinish
 * @property float $colorQtyFinishToYard
 *
 * @property float $qtyShippingSample
 */
class TrnMo extends \yii\db\ActiveRecord
{
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS
    const PACKING_METHOD_SINGLE_ROLL = 1;const PACKING_METHOD_DOUBLE_FOLDED = 2;
    /**
     * @return array
     */
    public static function packingMethodOptions(){
        return[
            self::PACKING_METHOD_SINGLE_ROLL => 'SINGLE ROLL',
            self::PACKING_METHOD_DOUBLE_FOLDED => 'DOUBLE FOLDED'
        ];
    }

    const SHIPPING_METHOD_BALE = 1;const SHIPPING_METHOD_CARTOON = 2;const SHIPPING_METHOD_LOSE = 3;
    /**
     * @return array
     */
    public static function shippingMethodOptions(){
        return[
            self::SHIPPING_METHOD_BALE => 'BALE',
            self::SHIPPING_METHOD_CARTOON => 'CARTOON',
            self::SHIPPING_METHOD_LOSE => 'LOSE'
        ];
    }

    const SHIPPING_SORTING_SOLID = 1;const SHIPPING_SORTING_ASSORTED = 2;
    /**
     * @return array
     */
    public static function shippingSortingOptions(){
        return[
            self::SHIPPING_SORTING_SOLID => 'SOLID',
            self::SHIPPING_SORTING_ASSORTED => 'ASSORTED'
        ];
    }

    const PLASTIC_VACUM = 1;const PLASTIC_NON_VACUM = 2;
    /**
     * @return array
     */
    public static function plasticOptions(){
        return[
            self::PLASTIC_VACUM => 'VACUM',
            self::PLASTIC_NON_VACUM => 'NON VACUM'
        ];
    }

    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;const STATUS_REJECTED = 4;const STATUS_CLOSED = 5;const STATUS_BATAL = 6;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Diposting',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_BATAL => 'Batal',
        ];
    }

    public static function persenGradingOptions(){
        $options = [];
    
        for ($i = 80; $i <= 100; $i++) {
            $options[$i] = $i . '%';
        }
    
        return $options;
    }
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mo';
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
            [['sc_id', 'sc_greige_id', 'date', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'est_produksi', 'est_packing', 'target_shipment', 'status','persentase_grading'], 'required'],
            [['sc_id', 'sc_greige_id', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'process', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'jenis_gudang'], 'integer'],
            [['date', 'est_produksi', 'est_packing', 'target_shipment'], 'date', 'format'=>'php:Y-m-d'],
            [['strike_off', 'face_stamping', 'closed_note', 'reject_notes', 'batal_note', 'note'], 'string'],
            [['heat_cut', 'foil', 'joint', 'jet_black'], 'boolean'],
            ['jenis_gudang', 'default', 'value'=>TrnStockGreige::JG_FRESH],
            [['no_lab_dip', 'handling', 'no', 're_wo', 'design', 'article', 'sulam_pinggir', 'selvedge_stamping', 'selvedge_continues', 'side_band', 'tag', 'hanger', 'label', 'folder', 'album', 'arsip', 'piece_length'], 'string', 'max' => 255],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['approval_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approval_id' => 'id']],
            [['closed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['closed_by' => 'id']],
            [['batal_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['batal_by' => 'id']],
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
            'process' => 'Process',
            'approval_id' => 'Approval ID',
            'approved_at' => 'Approved At',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            're_wo' => 'Re Wo',
            'design' => 'Design',
            'article' => 'Article',
            'strike_off' => 'Strike Off',
            'heat_cut' => 'Heat Cut',
            'sulam_pinggir' => 'Sulam Pinggir',
            'border_size' => 'Border Size',
            'block_size' => 'Block Size',
            'foil' => 'Foil',
            'face_stamping' => 'Face Stamping',
            'selvedge_stamping' => 'Selvedge Stamping',
            'selvedge_continues' => 'Selvedge Continues',
            'side_band' => 'Side Band',
            'tag' => 'Tag',
            'hanger' => 'Hanger',
            'label' => 'Label',
            'folder' => 'Folder',
            'album' => 'Album',
            'joint' => 'Joint',
            'joint_qty' => 'Joint Qty',
            'packing_method' => 'Packing Method',
            'shipping_method' => 'Shipping Method',
            'shipping_sorting' => 'Shipping Sorting',
            'plastic' => 'Plastic',
            'arsip' => 'Arsip',
            'jet_black' => 'Jet Black',
            'piece_length' => 'Piece Length',
            'est_produksi' => 'Est Produksi',
            'est_packing' => 'Est Packing',
            'target_shipment' => 'Target Shipment',
            'posted_at' => 'Posted At',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'closed_note' => 'Closed Note',
            'reject_notes' => 'Reject Notes',
            'batal_at' => 'Batal At',
            'batal_by' => 'Batal By',
            'batal_note' => 'Batal Note',
            'status' => 'Status',
            'note' => 'Note',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'jenis_gudang' => 'Jenis Gudang',
            'persen_grading' => 'Persen Grading Pengkartuan Greige A/B'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['mo_id' => 'id']);
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
    public function getApproval()
    {
        return $this->hasOne(User::className(), ['id' => 'approval_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClosedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'closed_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatalBy()
    {
        return $this->hasOne(User::className(), ['id' => 'batal_by']);
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
    public function getTrnMoColors()
    {
        return $this->hasMany(TrnMoColor::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoMemos()
    {
        return $this->hasMany(TrnMoMemo::className(), ['mo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['mo_id' => 'id']);
    }

    /**
     * wo yang masuk perhitungan statistik
     * berstatus bukan batal
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWosAktif()
    {
        return $this->getTrnWos()->where(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['mo_id' => 'id']);
    }

    /**
     * Jumlah color pada wo yang diperhitungkan untuk statistik
     * wo berstatus bukan batal
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsAktif()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsDraft()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_DRAFT]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsOk()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_APPROVED]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsBatal()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_BATAL]);
    }

    /**
     * @return float
     */
    public function getTrnWoColorsAktifQty()
    {
        $qty = $this->getTrnWoColorsAktif()->sum('trn_wo_color.qty');
        return $qty > 0 ? $qty : 0;
    }

    /**
     * @return float
     */
    public function getTrnWoColorsDraftQty()
    {
        $qty = $this->getTrnWoColorsDraft()->sum('trn_wo_color.qty');
        return $qty > 0 ? $qty : 0;
    }

    /**
     * @return float
     */
    public function getTrnWoColorsOkQty()
    {
        $qty = $this->getTrnWoColorsOk()->sum('trn_wo_color.qty');
        return $qty > 0 ? $qty : 0;
    }

    /**
     * @return float
     */
    public function getWoColorsBatalQty()
    {
        $qty = $this->getTrnWoColorsBatal()->sum('trn_wo_color.qty');
        return $qty > 0 ? $qty : 0;
    }

    /**
     * @return string
     */
    public function getApprovalStatus()
    {
        $paramApproval = Yii::$app->params['approval_status'];
        switch ($this->status){
            case self::STATUS_POSTED:
                return $paramApproval['menunggu'];
            case self::STATUS_APPROVED:
                return $paramApproval['disetujui'];
            case self::STATUS_REJECTED:
                return $paramApproval['ditolak'];
            default:
                return $paramApproval['belum_diajukan'];
        }
    }

    /**
     * @return string
     */
    public function getApprovalName()
    {
        return $this->getApproval()->select('full_name')->one()['full_name'];
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->getCreatedBy()->select('full_name')->one()['full_name'];
    }

    /**
     * @return string
     */
    public function getUpdatorName()
    {
        return $this->getUpdatedBy()->select('full_name')->one()['full_name'];
    }

    /**
     * @return string
     */
    public function getMarketingName()
    {
        return $this->sc->getMarketing()->select('full_name')->one()['full_name'];
    }

    /**
     * @return float
     */
    public function getColorQty()
    {
        $qty = $this->getTrnMoColors()->sum('qty');
        return $qty === null ? 0 : (float)$qty;
    }

    /**
     * @return float
     */
    public function getColorQtyBatchToUnit()
    {
        return $this->colorQty * (float)$this->scGreige->greigeGroup->qty_per_batch;
    }

    /**
     * @return float
     */
    public function getColorQtyFinish()
    {
        return $this->colorQty * (float)$this->scGreige->greigeGroup->qtyFinish;
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getColorQtyFinishToYard()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->colorQtyFinish);
            case MstGreigeGroup::UNIT_YARD:
                return $this->colorQtyFinish;
            default:
                return 0;
        }
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getColorQtyFinishToMeter()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return $this->colorQtyFinish;
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->colorQtyFinish);
            default:
                return 0;
        }
    }

    public function getQtyShippingSample(){
        $result = '';

        $qty = $this->colorQty;
        $qtyFinish = $this->colorQtyFinishToYard;

        switch ($this->scGreige->process){
            case TrnScGreige::PROCESS_DYEING:
                if(1 <= $qty && $qty <= 2){
                    $result = '5 M / 3 HDF / 7 HSF';
                }else{
                    $result = '15 M / 9 HDF / 21 HSF';
                }
                break;
            case TrnScGreige::PROCESS_PRINTING:
                if(1 <= $qtyFinish && $qtyFinish <= 1500){
                    $result = 1;
                }elseif(1501 < $qtyFinish && $qtyFinish <= 2300){
                    $result = 2;
                }elseif(2301 < $qtyFinish && $qtyFinish <= 3100){
                    $result = 3;
                }elseif(3101 < $qtyFinish && $qtyFinish <= 3900){
                    $result = 4;
                }elseif(3901 < $qtyFinish && $qtyFinish <= 4700){
                    $result = 5;
                }elseif(4701 < $qtyFinish && $qtyFinish <= 5500){
                    $result = 6;
                }else{
                    $result = 7;
                }
                break;
        }

        return $result;
    }

    private function setNoUrut(){
        $scGreige = $this->scGreige;
        $sc = $this->sc;

        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        //$m = date("n", $ts); //bulan satu digit 1-12
        //$ym = $y.'-'.$m;

        /* @var $lastData TrnMo*/
        $lastData = TrnMo::find()->joinWith('scGreige.sc')
            ->where([
                'and',
                ['not', ['trn_mo.no_urut' => null]],
                new Expression('EXTRACT(year FROM "'.self::tableName().'"."date") = '.$y), //nomor per tahun
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
                ['trn_sc.jenis_order' => $sc->jenis_order],
                ['trn_sc.tipe_kontrak' => $sc->tipe_kontrak],
                ['trn_sc_greige.process' => $scGreige->process],
            ])
            ->orderBy(['trn_mo.no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }

    public function setNoMO(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $scGreige = $this->scGreige;
        $sc = $this->sc;

        switch ($sc->tipe_kontrak){
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $tipeKontrakCode = 'L';
                break;
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $tipeKontrakCode = 'E';
                break;
            default:
                $tipeKontrakCode = '-';
        }

        switch ($scGreige->process){
            case TrnScGreige::PROCESS_DYEING:
                $jenisProsesCode = 'D';
                break;
            case TrnScGreige::PROCESS_PRINTING:
                $jenisProsesCode = 'P';
                break;
            case TrnScGreige::PROCESS_PFP:
                $jenisProsesCode = 'F';
                break;
            default:
                $jenisProsesCode = '-';
        }

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $empCode = 'M'.$sc->marketing->id;
        $this->no = "{$empCode}/{$yearTwoDigit}{$month}/{$tipeKontrakCode}/{$jenisProsesCode}-{$noUrut}";
    }
}
