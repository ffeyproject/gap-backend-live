<?php

namespace common\models\ar;

use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_stock_pfp_pindah_printing_log".
 *
 * @property int $id
 * @property int $stock_id
 * @property int $greige_id
 * @property float $panjang_m
 * @property int|null $jenis_printing_asal
 * @property int $jenis_printing_tujuan
 * @property string|null $note
 * @property int $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnStockGreige $stock
 * @property MstGreige $greige
 * @property User $createdBy
 * @property string $jenisPrintingAsalName
 * @property string $jenisPrintingTujuanName
 */
class TrnStockPfpPindahPrintingLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_stock_pfp_pindah_printing_log';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_id', 'greige_id', 'jenis_printing_tujuan'], 'required'],
            [['stock_id', 'greige_id', 'jenis_printing_asal', 'jenis_printing_tujuan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['stock_id', 'greige_id', 'jenis_printing_asal', 'jenis_printing_tujuan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['panjang_m'], 'number'],
            [['note'], 'string'],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnStockGreige::className(), 'targetAttribute' => ['stock_id' => 'id']],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_id' => 'Stock ID',
            'greige_id' => 'Greige',
            'panjang_m' => 'Panjang (M)',
            'jenis_printing_asal' => 'Dari Jenis Printing',
            'jenis_printing_tujuan' => 'Ke Jenis Printing',
            'note' => 'Catatan / Alasan',
            'created_at' => 'Waktu Pindah',
            'created_by' => 'Dipindahkan Oleh',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'jenisPrintingAsalName' => 'Dari Jenis Printing',
            'jenisPrintingTujuanName' => 'Ke Jenis Printing',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return string
     */
    public function getJenisPrintingAsalName()
    {
        $opts = TrnStockGreige::jenisPrintingOptions();
        return isset($opts[$this->jenis_printing_asal]) ? $opts[$this->jenis_printing_asal] : '-';
    }

    /**
     * @return string
     */
    public function getJenisPrintingTujuanName()
    {
        $opts = TrnStockGreige::jenisPrintingOptions();
        return isset($opts[$this->jenis_printing_tujuan]) ? $opts[$this->jenis_printing_tujuan] : '-';
    }
}
