<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_stock_greige".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $asal_greige 1=Water Jet Loom, 2=Beli Lokal, 3=Rapier, 4=Beli Import, 5=Lain-lain
 * @property string $no_lapak
 * @property int $grade 1=A,2=B,3=C,4=D,5=E
 * @property string $lot_lusi
 * @property string $lot_pakan
 * @property string $no_set_lusi
 * @property int $panjang_m kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)
 * @property int $status_tsd 1=sm(salur muda),2=st(salur tua),3=sa(salur abnormal
 * @property string $no_document
 * @property string $pengirim
 * @property string $mengetahui
 * @property string|null $note
 * @property int $status 1=Pending, 2=Valid, 3=On Process Card, 4=Dipotong, 5=Dikeluarkan Dari Gudang
 * @property string $date
 * @property int $jenis_gudang 1=Gudang Fresh, 2=Gudang WIP, 3=Gudang PFP, 4=Gudang Ex Finish
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $nomor_wo Hanya berlaku untuk jenis gudang ex finish
 * @property int|null $keputusan_qc mereferensi ke TrnReturBuyer::keputusanQcOptions() khusus ntuk jenis gudang ex finish
 * @property string|null $color
 *
 * @property TrnGreigeKeluarItem[] $trnGreigeKeluarItems
 * @property TrnGreigeKeluar[] $greigeKeluars
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItems
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnPotongGreige[] $trnPotongGreiges
 * @property TrnPotongGreigeItem[] $trnPotongGreigeItems
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 */
class TrnStockGreige extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_stock_greige';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'no_lapak', 'grade', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'status_tsd', 'no_document', 'pengirim', 'mengetahui', 'date', 'created_at', 'created_by'], 'required'],
            [['greige_group_id', 'greige_id', 'asal_greige', 'grade', 'panjang_m', 'status_tsd', 'status', 'jenis_gudang', 'created_at', 'created_by', 'updated_at', 'updated_by', 'keputusan_qc'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'asal_greige', 'grade', 'panjang_m', 'status_tsd', 'status', 'jenis_gudang', 'created_at', 'created_by', 'updated_at', 'updated_by', 'keputusan_qc'], 'integer'],
            [['note'], 'string'],
            [['date'], 'safe'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'no_document', 'pengirim', 'mengetahui', 'nomor_wo', 'color'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
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
            'asal_greige' => 'Asal Greige',
            'no_lapak' => 'No Lapak',
            'grade' => 'Grade',
            'lot_lusi' => 'Lot Lusi',
            'lot_pakan' => 'Lot Pakan',
            'no_set_lusi' => 'No Set Lusi',
            'panjang_m' => 'Qty',
            'status_tsd' => 'Status Tsd',
            'no_document' => 'No Document',
            'pengirim' => 'Pengirim',
            'mengetahui' => 'Mengetahui',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'jenis_gudang' => 'Jenis Gudang',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'nomor_wo' => 'Nomor Wo',
            'keputusan_qc' => 'Keputusan Qc',
            'color' => 'Color',
        ];
    }

    /**
     * Gets query for [[TrnGreigeKeluarItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnGreigeKeluarItems()
    {
        return $this->hasMany(TrnGreigeKeluarItem::className(), ['stock_greige_id' => 'id']);
    }

    /**
     * Gets query for [[GreigeKeluars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeKeluars()
    {
        return $this->hasMany(TrnGreigeKeluar::className(), ['id' => 'greige_keluar_id'])->viaTable('trn_greige_keluar_item', ['stock_greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesCelupItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelupItems()
    {
        return $this->hasMany(TrnKartuProsesCelupItem::className(), ['stock_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['stock_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['stock_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPrintingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['stock_id' => 'id']);
    }

    /**
     * Gets query for [[TrnPotongGreiges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnPotongGreiges()
    {
        return $this->hasMany(TrnPotongGreige::className(), ['stock_greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnPotongGreigeItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnPotongGreigeItems()
    {
        return $this->hasMany(TrnPotongGreigeItem::className(), ['stock_greige_id' => 'id']);
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[GreigeGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }
}
