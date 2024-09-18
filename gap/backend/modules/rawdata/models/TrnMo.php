<?php

namespace backend\modules\rawdata\models;

use Yii;

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
 * @property int $jenis_gudang mereferensi ke tabel trn_stock_greige::jenisGudangOptions
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
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
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
 * @property TrnWoColor[] $trnWoColors
 */
class TrnMo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'process', 'date', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'est_produksi', 'est_packing', 'target_shipment', 'status'], 'required'],
            [['sc_id', 'sc_greige_id', 'process', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'jenis_gudang', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'process', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'jenis_gudang', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['date', 'est_produksi', 'est_packing', 'target_shipment'], 'safe'],
            [['strike_off', 'face_stamping', 'closed_note', 'reject_notes', 'batal_note', 'note'], 'string'],
            [['heat_cut', 'foil', 'joint', 'jet_black'], 'boolean'],
            [['no', 're_wo', 'design', 'article', 'sulam_pinggir', 'selvedge_stamping', 'selvedge_continues', 'side_band', 'tag', 'hanger', 'label', 'folder', 'album', 'arsip', 'piece_length'], 'string', 'max' => 255],
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
            'jenis_gudang' => 'Jenis Gudang',
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
        ];
    }

    /**
     * Gets query for [[TrnInspectings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesMaklons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesMaklonItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPrintings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPrintingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['mo_id' => 'id']);
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
     * Gets query for [[Approval]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApproval()
    {
        return $this->hasOne(User::className(), ['id' => 'approval_id']);
    }

    /**
     * Gets query for [[ClosedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClosedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'closed_by']);
    }

    /**
     * Gets query for [[BatalBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatalBy()
    {
        return $this->hasOne(User::className(), ['id' => 'batal_by']);
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
     * Gets query for [[TrnMoColors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColors()
    {
        return $this->hasMany(TrnMoColor::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnMoMemos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoMemos()
    {
        return $this->hasMany(TrnMoMemo::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnWos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['mo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnWoColors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['mo_id' => 'id']);
    }
}
