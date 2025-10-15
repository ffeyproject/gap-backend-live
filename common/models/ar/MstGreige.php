<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

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
 * @property float $available
 * @property float $booked_wo
 * @property float $booked
 * @property float $stock_pfp
 * @property float $booked_pfp
 * @property float $stock_wip
 * @property float $booked_wip
 * @property float $stock_ef
 * @property float $booked_ef
 *
 * @property MstGreigeGroup $group
 * @property TrnStockGreige[] $trnStockGreiges
 * @property TrnWo[] $trnWos
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
            [['group_id', 'nama_kain'], 'required'],
            [['group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['gap', 'stock', 'available', 'booked_wo', 'stock_pfp', 'stock_wip', 'stock_ef', 'booked', 'booked_pfp', 'booked_wip', 'booked_ef','booked_opfp','available_pfp', 'status_weaving', 'stock_opname'], 'number'],
            [['gap', 'stock', 'available', 'booked_wo', 'stock_pfp', 'stock_wip', 'stock_ef', 'booked', 'booked_pfp', 'booked_wip', 'booked_ef','booked_opfp','available_pfp'], 'default', 'value' => 0],
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
            'available' => 'Available',
            'booked_wo' => 'Booked WO',
            'booked' => 'Booked',
            'stock_pfp' => 'Stock PFP',
            'booked_pfp' => 'Booked PFP',
            'stock_wip' => 'Stock WIP',
            'booked_wip' => 'Booked WIP',
            'stock_ef' => 'Stock Ex Finish',
            'booked_ef' => 'Booked Ex Finish',
            'booked_opfp' => 'Booked Order PFP',
            'available_pfp' => 'Available PFP',
            'status_weaving' => 'Status Weaving',
            'stock_opname' => 'Stock Opname',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // Saat pertama kali dibuat, available = stock
                $this->available = $this->stock;
            }
            return true;
        }
        return false;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnStockGreiges()
    {
        return $this->hasMany(TrnStockGreige::className(), ['greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['greige_id' => 'id']);
    }

    public function getStockPerGrade()
    {
        return TrnStockGreige::getStockPerGrade($this->id);
    }

    public function getTrnGudangInspect()
    {
        return $this->hasMany(TrnGudangInspect::className(), ['greige_id' => 'id']);
    }

    public function getTrnGudangInspectPosted()
    {
        return $this->hasMany(TrnGudangInspect::className(), ['greige_id' => 'id'])->where(['status' => TrnGudangInspect::STATUS_POSTED]);
    }


    public function getTotalPanjangMGudangInspect()
    {
        $total = 0;
        foreach ($this->trnGudangInspectPosted as $inspect) {
            foreach ($inspect->trnGudangInspectItemsNotOut as $item) {
                $total += $item->panjang_m;
            }
        }
        return $total;
    }

    public function getTrnGudangStockOpnamePosted()
    {
        return $this->hasMany(TrnGudangStockOpname::className(), ['greige_id' => 'id'])->where(['status' => TrnGudangStockOpname::STATUS_POSTED]);
    }


    public function getTotalPanjangMGudangStockOpname()
    {
        $total = 0;
        foreach ($this->trnGudangStockOpnamePosted as $tso) {
            foreach ($tso->trnGudangStockOpnameItemsNotOut as $item) {
                $total += $item->panjang_m;
            }
        }
        return $total;
    }

    public function addBackToStock($jumlah)
    {
        $this->stock = (float)$this->stock + (float)$jumlah;
        $this->available = (float)$this->available + (float)$jumlah;

        return $this->save(false, ['stock', 'available']);
    }

    public function addBackToStockOpname($jumlah)
    {
        $this->stock = (float)$this->stock + (float)$jumlah;
        $this->available = (float)$this->available + (float)$jumlah;
        $this->stock_opname = (float)$this->stock_opname + (float)$jumlah;

        return $this->save(false, ['stock', 'available', 'stock_opname']);
    }

    public function reduceBackWoToStock($jumlah)
    {
        $this->available = (float)$this->available + (float)$jumlah;
        $this->booked_wo = (float)$this->booked_wo - (float)$jumlah;

        return $this->save(false, ['available', 'booked_wo']);
    }

    /**
     * @return float
     * tidak dipakai lagi, sudah digantikan oleh kolom tambahan "available"
     */
    /*public function getAvailableStock()
    {
        return (float)$this->stock - (float)$this->booked;
    }*/

        /**
     * Daftar status weaving
     * @return array
     */
    public static function getStatusWeavingList()
    {
        return [
            1 => 'Non Moving',
            2 => 'Slow Moving',
            3 => 'Fast Moving',
        ];
    }

    /**
     * Ambil label status weaving
     * @return string|null
     */
    public function getStatusWeavingLabel()
    {
        $list = self::getStatusWeavingList();
        return $list[$this->status_weaving] ?? null;
    }
}