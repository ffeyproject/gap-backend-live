<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_greige".
 *
 * @property int $id
 * @property int $group_id
 * @property string $nama_kain
 * @property string|null $alias
 * @property string|null $no_dok_referensi
 * @property float $gap
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property bool|null $aktif
 * @property float $stock
 * @property float|null $booked
 * @property float $stock_pfp
 * @property float $booked_pfp
 * @property float $stock_wip
 * @property float $booked_wip
 * @property float $stock_ef ex finish
 * @property float $booked_ef ex finish
 *
 * @property MstGreigeGroup $group
 * @property MutasiExFinish[] $mutasiExFinishes
 * @property MutasiExFinishItem[] $mutasiExFinishItems
 * @property TrnBuyPfp[] $trnBuyPfps
 * @property TrnBuyPfpItem[] $trnBuyPfpItems
 * @property TrnKartuProsesPfp[] $trnKartuProsesPfps
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property TrnOrderPfp[] $trnOrderPfps
 * @property TrnStockGreige[] $trnStockGreiges
 * @property TrnWo[] $trnWos
 * @property TrnWoColor[] $trnWoColors
 */
class MstGreige extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_greige';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'nama_kain'], 'required'],
            [['group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['gap', 'stock', 'booked', 'stock_pfp', 'booked_pfp', 'stock_wip', 'booked_wip', 'stock_ef', 'booked_ef'], 'number'],
            [['aktif'], 'boolean'],
            [['nama_kain', 'alias', 'no_dok_referensi'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            'nama_kain' => 'Nama Kain',
            'alias' => 'Alias',
            'no_dok_referensi' => 'No Dok Referensi',
            'gap' => 'Gap',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'aktif' => 'Aktif',
            'stock' => 'Stock',
            'booked' => 'Booked',
            'stock_pfp' => 'Stock Pfp',
            'booked_pfp' => 'Booked Pfp',
            'stock_wip' => 'Stock Wip',
            'booked_wip' => 'Booked Wip',
            'stock_ef' => 'Stock Ef',
            'booked_ef' => 'Booked Ef',
        ];
    }

    /**
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'group_id']);
    }

    /**
     * Gets query for [[MutasiExFinishes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishes()
    {
        return $this->hasMany(MutasiExFinish::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[MutasiExFinishItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishItems()
    {
        return $this->hasMany(MutasiExFinishItem::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnBuyPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfps()
    {
        return $this->hasMany(TrnBuyPfp::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnBuyPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnBuyPfpItems()
    {
        return $this->hasMany(TrnBuyPfpItem::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfps()
    {
        return $this->hasMany(TrnKartuProsesPfp::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPfpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnOrderPfps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnOrderPfps()
    {
        return $this->hasMany(TrnOrderPfp::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnStockGreiges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnStockGreiges()
    {
        return $this->hasMany(TrnStockGreige::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnWos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['greige_id' => 'id']);
    }

    /**
     * Gets query for [[TrnWoColors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['greige_id' => 'id']);
    }
}
