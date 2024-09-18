<?php
namespace common\models\ar;

use Yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "trn_sc".
 *
 * @property int $id
 * @property int $cust_id
 * @property int $jenis_order 1=FRESH ORDER, 2=MAKLOON, 3=BARANG JADI, 4=STOCK
 * @property int $currency 1=IDR, 2=USD
 * @property int|null $bank_acct_id
 * @property int $direktur_id
 * @property int $manager_id
 * @property int $marketing_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $tipe_kontrak 1=LOKAL, 2=EXPORT
 * @property string $date
 * @property int $pmt_term
 * @property string $pmt_method
 * @property int $ongkos_angkut 1=Pemesan, 2=Penjual/Pabrik, 3=FOB, 4=CNF, 5=CIF
 * @property string $due_date
 * @property string $delivery_date
 * @property string $destination
 * @property string|null $packing
 * @property bool|null $jet_black
 * @property string|null $no_po
 * @property float $disc_grade_b
 * @property float $disc_piece_kecil
 * @property string|null $consignee_name
 * @property int|null $apv_dir_at
 * @property string|null $reject_note_dir
 * @property int|null $apv_mgr_at
 * @property string|null $reject_note_mgr
 * @property string|null $notify_party
 * @property string|null $buyer_name_in_invoice
 * @property string|null $note
 * @property int|null $posted_at
 * @property int|null $closed_at
 * @property int|null $closed_by
 * @property string|null $closed_note
 * @property int|null $batal_at
 * @property int|null $batal_by
 * @property string|null $batal_note
 * @property int $status 1=draft, 2=posted, 3=approved by dir, 4=approved by mgr, 5=approved, 6=rejected, 7=closed, 8=batal
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnMo[] $trnMos
 * @property TrnMoColor[] $trnMoColors
 * @property MstBankAccount $bankAcct
 * @property MstCustomer $cust
 * @property User $direktur
 * @property User $manager
 * @property User $marketing
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnScAgen[] $trnScAgens
 * @property TrnScGreige[] $trnScGreiges
 * @property TrnScKomisi[] $trnScKomisis
 * @property TrnScMemo[] $trnScMemos
 * @property TrnWo[] $trnWos
 * @property TrnWoColor[] $trnWoColors
 *
 * @property string $marketingName
 * @property string $managerName
 * @property string $direkturName
 * @property string $bankAcctName
 * @property string $bankAcctAddress
 * @property string $creatorName
 * @property string $updatorName
 * @property string $customerName
 * @property string $customerCode
 * @property string $jenisOrderName
 * @property string $currencyName
 *
 * @property string $apvDirStatus
 * @property string $apvMgrStatus
 */
class TrnSc extends \yii\db\ActiveRecord
{
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS
    const JENIS_ORDER_FRESH_ORDER = 1;const JENIS_ORDER_MAKLOON = 2;const JENIS_ORDER_BARANG_JADI = 3;const JENIS_ORDER_STOK = 4;
    /**
     * @return array
     */
    public static function jenisOrderOptions(){
        return [
            self::JENIS_ORDER_FRESH_ORDER => 'Fresh Order',
            self::JENIS_ORDER_MAKLOON => 'Makloon',
            self::JENIS_ORDER_BARANG_JADI => 'Barang Jadi',
            self::JENIS_ORDER_STOK => 'Stok',
        ];
    }

    const CURRENCY_IDR = 1;const CURRENCY_USD = 2;
    /**
     * @return array
     */
    public static function currencyOptions(){
        return [
            self::CURRENCY_IDR => 'IDR',
            self::CURRENCY_USD => 'USD',
        ];
    }

    const TIPE_KONTRAK_LOKAL = 1;const TIPE_KONTRAK_EXPORT = 2;
    /**
     * @return array
     */
    public static function tipeKontrakOptions(){
        return [
            self::TIPE_KONTRAK_LOKAL => 'Lokal',
            self::TIPE_KONTRAK_EXPORT => 'Export',
        ];
    }

    const ONGKOS_ANGKUT_PEMESAN = 1;const ONGKOS_ANGKUT_PENJUAL = 2;const ONGKOS_ANGKUT_FOB = 3;const ONGKOS_ANGKUT_CNF = 4;const ONGKOS_ANGKUT_CIF = 5;
    /**
     * @return array
     */
    public static function ongkosAngkutOptions(){
        return [
            self::ONGKOS_ANGKUT_PEMESAN => 'Pemesan',
            self::ONGKOS_ANGKUT_PENJUAL => 'Penjual/Pabrik',
            self::ONGKOS_ANGKUT_FOB => 'FOB',
            self::ONGKOS_ANGKUT_CNF => 'CNF',
            self::ONGKOS_ANGKUT_CIF => 'CIF',
        ];
    }

    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APV_DIR = 3;const STATUS_APV_MGR = 4;const STATUS_APPROVED = 5;const STATUS_REJECTED = 6;const STATUS_CLOSED = 7;const STATUS_BATAL = 8;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Diposting',
            self::STATUS_APV_DIR => 'Disetujui Direktur',
            self::STATUS_APV_MGR => 'Disetujui Manajer',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_BATAL => 'Batal',
        ];
    }
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc';
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
            [['cust_id', 'jenis_order', 'currency', 'direktur_id', 'manager_id', 'marketing_id', 'tipe_kontrak', 'date', 'pmt_term', 'pmt_method', 'ongkos_angkut', 'due_date', 'delivery_date', 'destination', 'status'], 'required'],
            [['cust_id', 'jenis_order', 'currency', 'bank_acct_id', 'direktur_id', 'manager_id', 'marketing_id', 'no_urut', 'tipe_kontrak', 'pmt_term', 'ongkos_angkut', 'apv_dir_at', 'apv_mgr_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['cust_id', 'jenis_order', 'currency', 'bank_acct_id', 'direktur_id', 'manager_id', 'marketing_id', 'no_urut', 'tipe_kontrak', 'pmt_term', 'ongkos_angkut', 'apv_dir_at', 'apv_mgr_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['date', 'due_date', 'delivery_date'], 'date', 'format'=>'php:Y-m-d'],
            [['destination', 'reject_note_dir', 'reject_note_mgr', 'notify_party', 'note', 'closed_note', 'batal_note'], 'string'],
            [['jet_black'], 'boolean'],
            [['disc_grade_b', 'disc_piece_kecil'], 'number'],
            [['no', 'pmt_method', 'packing', 'no_po', 'consignee_name', 'buyer_name_in_invoice'], 'string', 'max' => 255],
            [['bank_acct_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstBankAccount::className(), 'targetAttribute' => ['bank_acct_id' => 'id']],
            [['cust_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstCustomer::className(), 'targetAttribute' => ['cust_id' => 'id']],
            [['direktur_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['direktur_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager_id' => 'id']],
            [['marketing_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['marketing_id' => 'id']],
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
            'cust_id' => 'Cust ID',
            'jenis_order' => 'Jenis Order',
            'currency' => 'Currency',
            'bank_acct_id' => 'Bank Acct ID',
            'direktur_id' => 'Direktur ID',
            'manager_id' => 'Manager ID',
            'marketing_id' => 'Marketing ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'tipe_kontrak' => 'Tipe Kontrak',
            'date' => 'Date',
            'pmt_term' => 'Pmt Term',
            'pmt_method' => 'Pmt Method',
            'ongkos_angkut' => 'Ongkos Angkut',
            'due_date' => 'Due Date',
            'delivery_date' => 'Delivery Date',
            'destination' => 'Destination',
            'packing' => 'Packing',
            'jet_black' => 'Jet Black',
            'no_po' => 'No Po',
            'disc_grade_b' => 'Disc Grade B',
            'disc_piece_kecil' => 'Disc Piece Kecil',
            'consignee_name' => 'Consignee Name',
            'apv_dir_at' => 'Apv Dir At',
            'reject_note_dir' => 'Reject Note Dir',
            'apv_mgr_at' => 'Apv Mgr At',
            'reject_note_mgr' => 'Reject Note Mgr',
            'notify_party' => 'Notify Party',
            'buyer_name_in_invoice' => 'Buyer Name In Invoice',
            'note' => 'Note',
            'posted_at' => 'Posted At',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'closed_note' => 'Closed Note',
            'batal_at' => 'Batal At',
            'batal_by' => 'Batal By',
            'batal_note' => 'Batal Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColors()
    {
        return $this->hasMany(TrnMoColor::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankAcct()
    {
        return $this->hasOne(MstBankAccount::className(), ['id' => 'bank_acct_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCust()
    {
        return $this->hasOne(MstCustomer::className(), ['id' => 'cust_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirektur()
    {
        return $this->hasOne(User::className(), ['id' => 'direktur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketing()
    {
        return $this->hasOne(User::className(), ['id' => 'marketing_id']);
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
    public function getTrnScAgens()
    {
        return $this->hasMany(TrnScAgen::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScGreiges()
    {
        return $this->hasMany(TrnScGreige::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScKomisis()
    {
        return $this->hasMany(TrnScKomisi::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScMemos()
    {
        return $this->hasMany(TrnScMemo::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['sc_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getDirekturName()
    {
        return $this->direktur->full_name;
    }

    /**
     * @return string
     */
    public function getManagerName()
    {
        return $this->manager->full_name;
    }

    /**
     * @return string
     */
    public function getMarketingName()
    {
        return $this->marketing->full_name;
    }

    /**
     * @return string
     */
    public function getBankAcctName()
    {
        return $this->bankAcct !== null ? $this->bankAcct->bank_name : '';
    }

    /**
     * @return string
     */
    public function getBankAcctAddress()
    {
        return $this->bankAcct->address;
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->createdBy->full_name;
    }

    /**
     * @return string
     */
    public function getUpdatorName()
    {
        return $this->updatedBy->full_name;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->cust->name;
    }

    /**
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->cust->cust_no;
    }

    /**
     * @return string
     */
    public function getJenisOrderName()
    {
        return self::jenisOrderOptions()[$this->jenis_order];
    }

    /**
     * @return string
     */
    public function getCurrencyName()
    {
        return self::currencyOptions()[$this->currency];
    }

    /**
     * @return string
     */
    public function getApvDirStatus()
    {
        $paramApproval = Yii::$app->params['approval_status'];
        if($this->status == self::STATUS_POSTED){
            return $paramApproval['menunggu'];
        }else if($this->status == self::STATUS_APV_DIR){
            return $paramApproval['disetujui'];
        } else if($this->status == self::STATUS_APV_MGR){
            return $paramApproval['menunggu'];
        }else if($this->status == self::STATUS_APPROVED){
            return $paramApproval['disetujui'];
        }else if($this->status == self::STATUS_REJECTED){
            return $paramApproval['ditolak'];
        }

        return $paramApproval['belum_diajukan'];
    }

    /**
     * @return string
     */
    public function getApvMgrStatus()
    {
        $paramApproval = Yii::$app->params['approval_status'];
        if($this->status == self::STATUS_POSTED){
            return $paramApproval['menunggu'];
        }else if($this->status == self::STATUS_APV_DIR){
            return $paramApproval['disetujui'];
        } else if($this->status == self::STATUS_APV_MGR){
            return $paramApproval['disetujui'];
        }else if($this->status == self::STATUS_APPROVED){
            return $paramApproval['disetujui'];
        }else if($this->status == self::STATUS_REJECTED){
            return $paramApproval['ditolak'];
        }

        return $paramApproval['belum_diajukan'];
    }

    /**
     * @return boolean
     */
    public function getIsApproved(){
        return $this->status = self::STATUS_APPROVED;
    }

    public function setNoUrut(){
        $this->no_urut = 1;

        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnSc*/
        $lastData = TrnSc::find()
            ->select(['id', 'no_urut'])
            ->where(['not', ['no_urut' => null]])
            ->andWhere(new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''))
            //->andWhere(new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") = \''.$y.'\''))
            ->andWhere(['tipe_kontrak' => $this->tipe_kontrak])
            ->andWhere(['jenis_order' => $this->jenis_order])
            ->orderBy(['no_urut' => SORT_DESC])
            ->asArray()
            ->one()
        ;

        if(!empty($lastData)){
            $lastNoUrut = (int)$lastData['no_urut'];
            $this->no_urut = $lastNoUrut + 1;
        }
    }

    public function setNoSc(){
        //tahun 2 digit. tipe kontrak. jenis order. no urut 4 digit
        //{tahun 2 digit akhir}{bulan 2 digit}{tipe kontrak}{jenis order}{no urut 4 digit}
        $ts = strtotime($this->date);
        $y = date("y", $ts); //tahun dua digit
        $m = date("m", $ts);

        switch ($this->tipe_kontrak){
            case self::TIPE_KONTRAK_LOKAL:
                $tipeKontrakCode = 'L';
                break;
            case self::TIPE_KONTRAK_EXPORT:
                $tipeKontrakCode = 'E';
                break;
            default:
                $tipeKontrakCode = '';
        }

        switch ($this->jenis_order){
            case self::JENIS_ORDER_FRESH_ORDER:
                $jenisOrderCode = '0';
                break;
            case self::JENIS_ORDER_MAKLOON:
                $jenisOrderCode = '1';
                break;
            case self::JENIS_ORDER_BARANG_JADI:
                $jenisOrderCode = '2';
                break;
            case self::JENIS_ORDER_STOK:
                $jenisOrderCode = '3';
                break;
            default:
                $jenisOrderCode = '-';
        }

        $this->no = $y.$m.$tipeKontrakCode.$jenisOrderCode.sprintf("%04s", $this->no_urut);
    }

    /**
     * @throws Exception
     */
    public function getHiddenFormTokenField() {
        $token = Yii::$app->getSecurity()->generateRandomString();
        $token = str_replace('+', '.', base64_encode($token));

        Yii::$app->session->set(Yii::$app->params['form_token_param'], $token);;
        return Html::hiddenInput(Yii::$app->params['form_token_param'], $token);
    }

    /**
     * @return float
     */
    public function getQtyScGreige() {
        $qty = $this->getTrnScGreiges()->sum('qty');
        if($qty > 0){
            return $qty;
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getQtyMoColorNotBatal() {
        $qty = $this->getTrnMoColors()->joinWith('mo')->where(['<>', 'trn_mo.status', TrnMo::STATUS_BATAL])->sum('trn_mo_color.qty');
        if($qty > 0){
            return $qty;
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getQtyWoColorNotBatal() {
        $qty = $this->getTrnWoColors()->joinWith('wo')->where(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL])->sum('trn_wo_color.qty');
        if($qty > 0){
            return $qty;
        }

        return 0;
    }
}
