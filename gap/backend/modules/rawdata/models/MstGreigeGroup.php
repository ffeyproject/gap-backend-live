<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_greige_group".
 *
 * @property int $id
 * @property int $jenis_kain 1=Suiting Men 2=Suiting Ladies 3=Printing 4=Kniting 5=Georgette 6=Lain-lain
 * @property string $nama_kain
 * @property float $qty_per_batch
 * @property int $unit 1=YARD 2=METER 3=PCS 4=KILOGRAM
 * @property float $nilai_penyusutan
 * @property string|null $gramasi_kain
 * @property string|null $sulam_pinggir
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property bool|null $aktif
 *
 * @property MstGreige[] $mstGreiges
 * @property MutasiExFinish[] $mutasiExFinishes
 * @property MutasiExFinishItem[] $mutasiExFinishItems
 * @property TrnBuyPfp[] $trnBuyPfps
 * @property TrnBuyPfpItem[] $trnBuyPfpItems
 * @property TrnKartuProsesPfp[] $trnKartuProsesPfps
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property TrnOrderPfp[] $trnOrderPfps
 * @property TrnScGreige[] $trnScGreiges
 * @property TrnStockGreige[] $trnStockGreiges
 */
class MstGreigeGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_greige_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_kain', 'nama_kain', 'unit'], 'required'],
            [['jenis_kain', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['jenis_kain', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['qty_per_batch', 'nilai_penyusutan'], 'number'],
            [['aktif'], 'boolean'],
            [['nama_kain', 'sulam_pinggir'], 'string', 'max' => 255],
            [['gramasi_kain'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_kain' => 'Jenis Kain',
            'nama_kain' => 'Nama Kain',
            'qty_per_batch' => 'Qty Per Batch',
            'unit' => 'Unit',
            'nilai_penyusutan' => 'Nilai Penyusutan',
            'gramasi_kain' => 'Gramasi Kain',
            'sulam_pinggir' => 'Sulam Pinggir',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'aktif' => 'Aktif',
        ];
    }

    /**
     * Gets query for [[MstGreiges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMstGreiges()
    {
        return $this->hasMany(MstGreige::className(), ['group_id' => 'id']);
    }

    /**
     * Gets query for [[MutasiExFinishes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishes()
    {
        return $this->hasMany(MutasiExFinish::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[MutasiExFinishItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishItems()
    {
        return $this->hasMany(MutasiExFinishItem::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnBuyPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfps()
    {
        return $this->hasMany(TrnBuyPfp::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnBuyPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfpItems()
    {
        return $this->hasMany(TrnBuyPfpItem::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfps()
    {
        return $this->hasMany(TrnKartuProsesPfp::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnOrderPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnOrderPfps()
    {
        return $this->hasMany(TrnOrderPfp::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnScGreiges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScGreiges()
    {
        return $this->hasMany(TrnScGreige::className(), ['greige_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrnStockGreiges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnStockGreiges()
    {
        return $this->hasMany(TrnStockGreige::className(), ['greige_group_id' => 'id']);
    }
}
