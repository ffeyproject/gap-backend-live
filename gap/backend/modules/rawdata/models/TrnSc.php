<?php

namespace backend\modules\rawdata\models;

use Yii;

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
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
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
 */
class TrnSc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc';
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
            [['date', 'due_date', 'delivery_date'], 'safe'],
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
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['sc_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['sc_id' => 'id']);
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
}
