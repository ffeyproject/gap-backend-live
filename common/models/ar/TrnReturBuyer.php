<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_retur_buyer".
 *
 * @property int $id
 * @property int|null $jenis_gudang Mereferensi ke TrnGudangJadi::jenisGudangOptions()
 * @property int $customer_id
 * @property int|null $sc_id
 * @property int|null $sc_greige_id
 * @property int|null $mo_id
 * @property int|null $wo_id
 * @property string $date
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $pengirim
 * @property string|null $no_document
 * @property string|null $penanggungjawab
 * @property string|null $nama_qc
 * @property string $date_document
 * @property int $keputusan_qc 1=Retur belum diperiksa 2=Retur tidak diterima karena barang bagus 3=Retur diterima, tapi barang dapat diperbaiki 4=Retur diterima, tapi barang tidak dapat diperbaiki
 *
 * @property MstCustomer $customer
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property TrnReturBuyerItem[] $trnReturBuyerItems
 */
class TrnReturBuyer extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2; const STATUS_RE_DYEING = 3; const STATUS_REPAIR = 4; const STATUS_SELESAI_REPAIR = 5;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_RE_DYEING => 'Re Dyeing', self::STATUS_REPAIR => 'Repair', self::STATUS_SELESAI_REPAIR => 'Selesai Repair'];
    }

    const QC_DRAFT = 1; const QC_GOOD = 2; const QC_REPAIR = 3; const QC_REJECT = 4;
    /**
     * @return array
     */
    public static function keputusanQcOptions(){
        return [
            self::QC_DRAFT => 'Retur belum diperiksa',
            self::QC_GOOD => 'Retur tidak diterima karena barang bagus',
            self::QC_REPAIR => 'Retur diterima, tapi barang dapat diperbaiki',
            self::QC_REJECT => 'Retur diterima, tapi barang tidak dapat diperbaiki',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_retur_buyer';
    }

    /**
     * {@inheritdoc}
     */
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
            [['customer_id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['customer_id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['wo_id', 'date', 'no_document', 'penanggungjawab', 'nama_qc', 'date_document'], 'required'],

            [['date', 'date_document'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED]],

            ['keputusan_qc', 'default', 'value'=>self::QC_DRAFT],
            ['keputusan_qc', 'in', 'range' => [self::QC_DRAFT, self::QC_GOOD, self::QC_REPAIR, self::QC_REJECT]],

            ['unit', 'default', 'value'=>MstGreigeGroup::UNIT_METER],
            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            ['jenis_gudang', 'default', 'value'=>TrnGudangJadi::JENIS_GUDANG_LOKAL],
            ['jenis_gudang', 'in', 'range' => [TrnGudangJadi::JENIS_GUDANG_LOKAL, TrnGudangJadi::JENIS_GUDANG_EXPORT, TrnGudangJadi::JENIS_GUDANG_GRADE_B]],

            [['note'], 'string'],
            [['no', 'pengirim', 'no_document', 'penanggungjawab', 'nama_qc'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_gudang' => 'Jenis Gudang',
            'customer_id' => 'Customer ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'date' => 'Date',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'unit' => 'Unit',
            'note' => 'Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'pengirim' => 'Pengirim',
            'no_document' => 'Nomor Dokumen',
            'penanggungjawab' => 'Penanggung Jawab',
            'nama_qc' => 'QC',
            'date_document' => 'Tanggal Dokumen',
            'keputusan_qc' => 'Keputusan QC dan Marketing'
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(MstCustomer::className(), ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
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
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * Gets query for [[TrnReturBuyerItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnReturBuyerItems()
    {
        return $this->hasMany(TrnReturBuyerItem::className(), ['retur_buyer_id' => 'id']);
    }

    /**
     * Validasi item retur terhadap data pengiriman:
     * - surat jalan harus milik WO yang sama,
     * - satu roll tidak boleh muncul dua kali,
     * - qty per roll tidak boleh melebihi qty kirim roll tersebut,
     * - total retur per surat jalan (termasuk dokumen retur lain) tidak boleh melebihi total kirim,
     * - total retur per WO (termasuk item tanpa surat jalan) tidak boleh melebihi total kirim WO.
     * Error ditambahkan ke masing-masing item.
     *
     * @param TrnReturBuyerItem[] $modelsItem
     * @return bool
     */
    public function validateItems($modelsItem)
    {
        $valid = true;
        $stockIds = [];
        $qtyPerKirim = [];

        foreach ($modelsItem as $item) {
            if (empty($item->kirim_buyer_id) || $item->hasErrors()) {
                continue;
            }

            $kirimBuyer = TrnKirimBuyer::findOne($item->kirim_buyer_id);
            if ($kirimBuyer === null || $kirimBuyer->wo_id != $this->wo_id) {
                $item->addError('kirim_buyer_id', 'Surat jalan bukan untuk WO ini.');
                $valid = false;
                continue;
            }

            $qtyKirimUnit = self::convertQty($item->qty, $this->unit, $kirimBuyer->unit);
            if ($qtyKirimUnit === null) {
                $units = MstGreigeGroup::unitOptions();
                $item->addError('qty', 'Unit retur (' . ($units[$this->unit] ?? '-') . ') tidak bisa dikonversi ke unit surat jalan (' . ($units[$kirimBuyer->unit] ?? '-') . ').');
                $valid = false;
                continue;
            }

            if (!empty($item->stock_id)) {
                if (in_array($item->stock_id, $stockIds)) {
                    $item->addError('stock_id', 'Roll yang sama dipilih lebih dari sekali.');
                    $valid = false;
                    continue;
                }
                $stockIds[] = $item->stock_id;

                $kirimItem = TrnKirimBuyerItem::findOne(['kirim_buyer_id' => $item->kirim_buyer_id, 'stock_id' => $item->stock_id]);
                if ($kirimItem !== null && $qtyKirimUnit > $kirimItem->qty) {
                    $item->addError('qty', 'Qty retur melebihi qty kirim roll ini (' . Yii::$app->formatter->asDecimal($kirimItem->qty) . ').');
                    $valid = false;
                    continue;
                }
            }

            $qtyPerKirim[$item->kirim_buyer_id] = ($qtyPerKirim[$item->kirim_buyer_id] ?? 0) + $qtyKirimUnit;
        }

        foreach ($qtyPerKirim as $kirimBuyerId => $qtyRetur) {
            $kirimBuyer = TrnKirimBuyer::findOne($kirimBuyerId);

            $others = TrnReturBuyerItem::find()
                ->alias('i')
                ->innerJoin(self::tableName() . ' r', 'r.id = i.retur_buyer_id')
                ->select(['i.qty', 'r.unit'])
                ->where(['i.kirim_buyer_id' => $kirimBuyerId])
                ->andFilterWhere(['<>', 'r.id', $this->id])
                ->asArray()
                ->all();
            foreach ($others as $other) {
                $qtyRetur += (float)self::convertQty($other['qty'], $other['unit'], $kirimBuyer->unit);
            }

            if ($qtyRetur > $kirimBuyer->qtyKirim) {
                foreach ($modelsItem as $item) {
                    if ($item->kirim_buyer_id == $kirimBuyerId) {
                        $item->addError('qty', 'Total retur untuk surat jalan ini (' . Yii::$app->formatter->asDecimal($qtyRetur) . ') melebihi qty kirim (' . Yii::$app->formatter->asDecimal($kirimBuyer->qtyKirim) . ').');
                    }
                }
                $valid = false;
            }
        }

        return $this->validateTotalWo($modelsItem) && $valid;
    }

    /**
     * Total retur WO (dokumen ini + dokumen retur lain), dalam unit dokumen ini,
     * tidak boleh melebihi total kirim WO pada surat jalan yang sudah diposting.
     * @param TrnReturBuyerItem[] $modelsItem
     * @return bool
     */
    private function validateTotalWo($modelsItem)
    {
        $units = MstGreigeGroup::unitOptions();
        $qtyKirim = 0;
        foreach (self::kirimQtyPerUnit($this->wo_id) as $unit => $qty) {
            $converted = self::convertQty($qty, $unit, $this->unit);
            if ($converted === null) {
                $msg = 'Unit retur (' . ($units[$this->unit] ?? '-') . ') tidak bisa dibandingkan dengan unit pengiriman WO ini (' . ($units[$unit] ?? '-') . '). Ubah Unit retur menjadi ' . ($units[$unit] ?? '-') . '.';
                foreach ($modelsItem as $item) {
                    if (!$item->hasErrors('qty')) {
                        $item->addError('qty', $msg);
                    }
                }
                return false;
            }
            $qtyKirim += $converted;
        }

        $qtyRetur = 0;
        foreach ($modelsItem as $item) {
            $qtyRetur += (float)$item->qty;
        }
        $others = TrnReturBuyerItem::find()
            ->alias('i')
            ->innerJoin(self::tableName() . ' r', 'r.id = i.retur_buyer_id')
            ->select(['i.qty', 'r.unit'])
            ->where(['r.wo_id' => $this->wo_id])
            ->andFilterWhere(['<>', 'r.id', $this->id])
            ->asArray()
            ->all();
        foreach ($others as $other) {
            $qtyRetur += (float)self::convertQty($other['qty'], $other['unit'], $this->unit);
        }

        if ($qtyRetur > $qtyKirim) {
            $msg = 'Total retur WO ini (' . Yii::$app->formatter->asDecimal($qtyRetur) . ') melebihi total kirim WO (' . Yii::$app->formatter->asDecimal($qtyKirim) . ').';
            foreach ($modelsItem as $item) {
                if (!$item->hasErrors('qty')) {
                    $item->addError('qty', $msg);
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Total qty kirim WO (surat jalan sudah posting) dikelompokkan per unit.
     * @param int $woId
     * @return array [unit => qty]
     */
    public static function kirimQtyPerUnit($woId)
    {
        $rows = TrnKirimBuyerItem::find()
            ->alias('kbi')
            ->innerJoin(TrnKirimBuyer::tableName() . ' kb', 'kb.id = kbi.kirim_buyer_id')
            ->innerJoin(TrnKirimBuyerHeader::tableName() . ' h', 'h.id = kb.header_id')
            ->select(['qty' => 'SUM(kbi.qty)', 'unit' => 'kb.unit'])
            ->where(['kb.wo_id' => $woId, 'h.status' => TrnKirimBuyerHeader::STATUS_POSTED])
            ->groupBy('kb.unit')
            ->asArray()
            ->all();
        return array_map('floatval', \yii\helpers\ArrayHelper::map($rows, 'unit', 'qty'));
    }

    /**
     * @param float $qty
     * @param int $fromUnit
     * @param int $toUnit
     * @return float|null null jika unit tidak bisa dikonversi
     */
    public static function convertQty($qty, $fromUnit, $toUnit)
    {
        if ($fromUnit == $toUnit) {
            return $qty;
        }
        if ($fromUnit == MstGreigeGroup::UNIT_YARD && $toUnit == MstGreigeGroup::UNIT_METER) {
            return Converter::yardToMeter($qty);
        }
        if ($fromUnit == MstGreigeGroup::UNIT_METER && $toUnit == MstGreigeGroup::UNIT_YARD) {
            return Converter::meterToYard($qty);
        }
        return null;
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKirimMakloon*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
