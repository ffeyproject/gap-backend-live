<?php

namespace common\models\ar;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "trn_retur_buyer_item".
 *
 * Barang retur sering tidak bisa dipastikan berasal dari roll (stock) yang mana karena label hilang.
 * Karena itu surat jalan (kirim_buyer_id) dan roll asal (stock_id) sama-sama opsional,
 * qty tetap dibatasi oleh total kiriman WO (lihat TrnReturBuyer::validateItems()).
 * Roll asal:
 *  - diisi lewat scan QR / ID roll jika label masih ada,
 *  - dipilih dari daftar roll pada surat jalan jika bisa dicocokkan (qty/grade),
 *  - dibiarkan kosong jika memang tidak diketahui.
 *
 * @property int $id
 * @property int $retur_buyer_id
 * @property int|null $kirim_buyer_id Surat jalan asal (trn_kirim_buyer.id)
 * @property int|null $surat_jalan_ex_finish_id Surat jalan asal Ex-Finish (surat_jalan_ex_finish.id)
 * @property int|null $jual_ex_finish_item_id Item asal Ex-Finish (jual_ex_finish_item.id)
 * @property int|null $stock_id Roll asal yang dikirim (trn_gudang_jadi.id), NULL jika tidak diketahui
 * @property int|null $gudang_jadi_id Stok baru hasil retur (trn_gudang_jadi.id), terisi saat posting
 * @property int $qty
 * @property string|null $note
 * @property int $grade mereferensi ke TrnStockGreige::gradeOptions()
 *
 * @property TrnReturBuyer $returBuyer
 * @property TrnKirimBuyer $kirimBuyer
 * @property SuratJalanExFinish $suratJalanExFinish
 * @property JualExFinishItem $jualExFinishItem
 * @property TrnGudangJadi $stock
 * @property TrnGudangJadi $gudangJadi
 */
class TrnReturBuyerItem extends \yii\db\ActiveRecord
{
    /** Input retur banyak WO sekaligus, item dikelompokkan per WO menjadi dokumen retur masing-masing */
    const SCENARIO_MULTI_WO = 'multi-wo';

    /**
     * WO per item, hanya dipakai pada form input banyak WO (tidak disimpan, WO tersimpan di header retur)
     * @var int|null
     */
    public $wo_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_retur_buyer_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'grade'], 'required'],
            ['wo_id', 'required', 'on' => self::SCENARIO_MULTI_WO],
            ['wo_id', 'integer'],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['retur_buyer_id', 'qty', 'kirim_buyer_id', 'surat_jalan_ex_finish_id', 'jual_ex_finish_item_id', 'stock_id', 'gudang_jadi_id'], 'default', 'value' => null],
            [['retur_buyer_id', 'qty', 'kirim_buyer_id', 'surat_jalan_ex_finish_id', 'jual_ex_finish_item_id', 'stock_id', 'gudang_jadi_id'], 'integer'],
            ['qty', 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            [['note'], 'string'],

            ['grade', 'default', 'value'=>TrnStockGreige::GRADE_A],
            // range disamakan dengan TrnGudangJadi karena grade bisa terisi otomatis dari roll asal
            ['grade', 'in', 'range' => [TrnStockGreige::GRADE_A, TrnStockGreige::GRADE_B, TrnStockGreige::GRADE_C, TrnStockGreige::GRADE_D, TrnStockGreige::GRADE_E, TrnStockGreige::GRADE_NG, TrnStockGreige::GRADE_A_PLUS, TrnStockGreige::GRADE_A_ASTERISK, TrnStockGreige::GRADE_PUTIH]],

            [['retur_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnReturBuyer::className(), 'targetAttribute' => ['retur_buyer_id' => 'id']],
            [['kirim_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimBuyer::className(), 'targetAttribute' => ['kirim_buyer_id' => 'id']],
            [['surat_jalan_ex_finish_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuratJalanExFinish::className(), 'targetAttribute' => ['surat_jalan_ex_finish_id' => 'id']],
            [['jual_ex_finish_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => JualExFinishItem::className(), 'targetAttribute' => ['jual_ex_finish_item_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['stock_id' => 'id']],
            ['stock_id', 'validateStock', 'skipOnEmpty' => true],
        ];
    }

    /**
     * Memproses nilai kirim_buyer_id dan stock_id jika berasal dari Ex-Finish sebelum validasi.
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (is_string($this->kirim_buyer_id) && strpos($this->kirim_buyer_id, 'ef-') === 0) {
                $this->surat_jalan_ex_finish_id = (int)substr($this->kirim_buyer_id, 3);
                $this->kirim_buyer_id = null;
            } elseif (is_string($this->kirim_buyer_id) && strpos($this->kirim_buyer_id, 'kb-') === 0) {
                $this->kirim_buyer_id = (int)substr($this->kirim_buyer_id, 3);
                $this->surat_jalan_ex_finish_id = null;
            }

            if (is_string($this->stock_id) && (strpos($this->stock_id, 'efitem-') === 0 || strpos($this->stock_id, 'efitem_') === 0)) {
                $this->jual_ex_finish_item_id = (int)substr($this->stock_id, 7);
                $this->stock_id = null;
            }

            return true;
        }
        return false;
    }

    /**
     * Nilai identifier surat jalan untuk dropdown form (mendukung reguler & Ex-Finish).
     * @return string
     */
    public function getSjValue()
    {
        if (!empty($this->surat_jalan_ex_finish_id)) {
            return 'ef-' . $this->surat_jalan_ex_finish_id;
        }
        if (!empty($this->kirim_buyer_id)) {
            return (string)$this->kirim_buyer_id;
        }
        return '';
    }

    /**
     * Nilai identifier roll/item untuk dropdown form (mendukung reguler & Ex-Finish).
     * @return string
     */
    public function getRollValue()
    {
        if (!empty($this->jual_ex_finish_item_id)) {
            return 'efitem-' . $this->jual_ex_finish_item_id;
        }
        if (!empty($this->stock_id)) {
            return (string)$this->stock_id;
        }
        return '';
    }

    /**
     * Roll asal harus ada di surat jalan yang dipilih dan belum pernah diretur pada dokumen retur lain.
     * @param string $attribute
     */
    public function validateStock($attribute)
    {
        if ($this->hasErrors('kirim_buyer_id') || $this->hasErrors('surat_jalan_ex_finish_id')) {
            return;
        }
        if (empty($this->kirim_buyer_id) && empty($this->surat_jalan_ex_finish_id)) {
            $this->addError($attribute, 'Pilih surat jalan untuk roll ini.');
            return;
        }

        if (!empty($this->kirim_buyer_id) && !empty($this->stock_id)) {
            $kirimItem = TrnKirimBuyerItem::findOne(['kirim_buyer_id' => $this->kirim_buyer_id, 'stock_id' => $this->stock_id]);
            if ($kirimItem === null) {
                $this->addError($attribute, 'Roll tidak terdaftar pada surat jalan yang dipilih.');
                return;
            }

            $sudahDiretur = self::find()
                ->where(['stock_id' => $this->stock_id])
                ->andFilterWhere(['<>', 'retur_buyer_id', $this->retur_buyer_id])
                ->andFilterWhere(['<>', 'id', $this->id])
                ->with('returBuyer')
                ->one();
            if ($sudahDiretur !== null) {
                $noRetur = $sudahDiretur->returBuyer->no ?: ('ID ' . $sudahDiretur->retur_buyer_id);
                $this->addError($attribute, "Roll ini sudah diretur pada dokumen retur {$noRetur}.");
            }
        }

        if (!empty($this->surat_jalan_ex_finish_id) && !empty($this->jual_ex_finish_item_id)) {
            $jualItem = JualExFinishItem::findOne($this->jual_ex_finish_item_id);
            $sjEf = SuratJalanExFinish::findOne($this->surat_jalan_ex_finish_id);
            if ($jualItem === null || $sjEf === null || $jualItem->jual_id != $sjEf->memo_id) {
                $this->addError($attribute, 'Item tidak terdaftar pada surat jalan Ex-Finish yang dipilih.');
                return;
            }

            $sudahDiretur = self::find()
                ->where(['jual_ex_finish_item_id' => $this->jual_ex_finish_item_id])
                ->andFilterWhere(['<>', 'retur_buyer_id', $this->retur_buyer_id])
                ->andFilterWhere(['<>', 'id', $this->id])
                ->with('returBuyer')
                ->one();
            if ($sudahDiretur !== null) {
                $noRetur = $sudahDiretur->returBuyer->no ?: ('ID ' . $sudahDiretur->retur_buyer_id);
                $this->addError($attribute, "Item ini sudah diretur pada dokumen retur {$noRetur}.");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'retur_buyer_id' => 'Retur Buyer ID',
            'wo_id' => 'WO',
            'kirim_buyer_id' => 'Surat Jalan',
            'surat_jalan_ex_finish_id' => 'Surat Jalan Ex-Finish',
            'jual_ex_finish_item_id' => 'Item Ex-Finish',
            'stock_id' => 'Roll Asal',
            'gudang_jadi_id' => 'Stok Hasil Retur',
            'qty' => 'Qty',
            'note' => 'Note',
            'grade' => 'Grade',
        ];
    }

    /**
     * Gets query for [[ReturBuyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturBuyer()
    {
        return $this->hasOne(TrnReturBuyer::className(), ['id' => 'retur_buyer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKirimBuyer()
    {
        return $this->hasOne(TrnKirimBuyer::className(), ['id' => 'kirim_buyer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadi()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'gudang_jadi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuratJalanExFinish()
    {
        return $this->hasOne(SuratJalanExFinish::className(), ['id' => 'surat_jalan_ex_finish_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJualExFinishItem()
    {
        return $this->hasOne(JualExFinishItem::className(), ['id' => 'jual_ex_finish_item_id']);
    }

    /**
     * Daftar surat jalan (sudah posting) yang mengirim WO tersebut (mendukung Reguler dan Ex-Finish).
     * @param int $woId
     * @return array [kirim_buyer_id => label]
     */
    public static function kirimBuyerOptions($woId)
    {
        if (empty($woId)) {
            return [];
        }

        $units = MstGreigeGroup::unitOptions();
        $options = [];

        // 1. Surat Jalan Reguler (TrnKirimBuyer)
        $rows = TrnKirimBuyer::find()
            ->alias('kb')
            ->innerJoin(TrnKirimBuyerHeader::tableName() . ' h', 'h.id = kb.header_id')
            ->leftJoin(TrnKirimBuyerItem::tableName() . ' kbi', 'kbi.kirim_buyer_id = kb.id')
            ->select(['kb.id', 'kb.unit', 'h.no', 'h.date', 'qty' => 'SUM(kbi.qty)', 'roll' => 'COUNT(kbi.id)'])
            ->where(['kb.wo_id' => $woId, 'h.status' => TrnKirimBuyerHeader::STATUS_POSTED])
            ->groupBy(['kb.id', 'kb.unit', 'h.no', 'h.date'])
            ->orderBy(['h.date' => SORT_DESC, 'kb.id' => SORT_DESC])
            ->asArray()
            ->all();

        foreach ($rows as $row) {
            $unit = $units[$row['unit']] ?? '';
            $options[$row['id']] = "{$row['no']} ({$row['date']}) - {$row['roll']} roll, " . Yii::$app->formatter->asDecimal($row['qty']) . " {$unit}";
        }

        // 2. Surat Jalan Ex-Finish (SuratJalanExFinish)
        $wo = TrnWo::findOne($woId);
        if ($wo !== null) {
            $efRows = (new \yii\db\Query())
                ->select([
                    'sj_id' => 'sj.id',
                    'sj_no' => 'sj.no',
                    'sj_date' => 'sj.created_at',
                    'unit' => 'ji.unit',
                    'qty' => 'SUM(ji.qty)',
                    'items_count' => 'COUNT(ji.id)'
                ])
                ->from(SuratJalanExFinish::tableName() . ' sj')
                ->innerJoin(JualExFinish::tableName() . ' j', 'j.id = sj.memo_id')
                ->innerJoin(JualExFinishItem::tableName() . ' ji', 'ji.jual_id = j.id')
                ->where(['ji.no_wo' => $wo->no])
                ->groupBy(['sj.id', 'sj.no', 'sj.created_at', 'ji.unit'])
                ->orderBy(['sj.created_at' => SORT_DESC, 'sj.id' => SORT_DESC])
                ->all();

            foreach ($efRows as $row) {
                $unit = $units[$row['unit']] ?? '';
                $dateFormatted = !empty($row['sj_date']) ? date('Y-m-d', $row['sj_date']) : '-';
                $key = 'ef-' . $row['sj_id'];
                $options[$key] = "[Ex-Finish] {$row['sj_no']} ({$dateFormatted}) - {$row['items_count']} roll, " . Yii::$app->formatter->asDecimal($row['qty']) . " {$unit}";
            }
        }

        return $options;
    }

    /**
     * Daftar roll/item yang dikirim pada satu surat jalan, beserta penanda apakah sudah pernah diretur.
     * @param string|int $kirimBuyerId ID surat jalan reguler (int) atau Ex-Finish (string 'ef-...')
     * @param int|null $excludeReturId dokumen retur yang sedang diedit, roll miliknya tidak dianggap "sudah diretur"
     * @param int|null $woId
     * @return array
     */
    public static function rollList($kirimBuyerId, $excludeReturId = null, $woId = null)
    {
        if (empty($kirimBuyerId)) {
            return [];
        }

        // Jika Surat Jalan Ex-Finish
        if (is_string($kirimBuyerId) && strpos($kirimBuyerId, 'ef-') === 0) {
            $sjId = (int)substr($kirimBuyerId, 3);
            $sj = SuratJalanExFinish::findOne($sjId);
            if ($sj === null) {
                return [];
            }

            $query = JualExFinishItem::find()
                ->where(['jual_id' => $sj->memo_id]);

            if (!empty($woId)) {
                $wo = TrnWo::findOne($woId);
                if ($wo !== null) {
                    $query->andWhere(['no_wo' => $wo->no]);
                }
            }

            $items = $query->orderBy(['id' => SORT_ASC])->all();
            $units = MstGreigeGroup::unitOptions();
            $grades = TrnStockGreige::gradeOptions();

            $returned = self::find()
                ->select(['jual_ex_finish_item_id'])
                ->where(['jual_ex_finish_item_id' => ArrayHelper::getColumn($items, 'id')])
                ->andFilterWhere(['<>', 'retur_buyer_id', $excludeReturId])
                ->column();

            $list = [];
            foreach ($items as $item) {
                $unit = $units[$item->unit] ?? '';
                $gradeName = $grades[$item->grade] ?? '-';
                $isReturned = in_array($item->id, $returned);
                $list[] = [
                    'id' => 'efitem-' . $item->id,
                    'qr_code' => null,
                    'qty' => (float)$item->qty,
                    'grade' => $item->grade,
                    'grade_name' => $gradeName,
                    'color' => null,
                    'returned' => $isReturned,
                    'text' => "[Ex-Finish Item #{$item->id}] | " . Yii::$app->formatter->asDecimal($item->qty) . " {$unit} | {$gradeName}",
                ];
            }
            return $list;
        }

        $items = TrnKirimBuyerItem::find()
            ->where(['kirim_buyer_id' => (int)$kirimBuyerId])
            ->with('stock')
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $returned = self::find()
            ->select(['stock_id'])
            ->where(['stock_id' => ArrayHelper::getColumn($items, 'stock_id')])
            ->andFilterWhere(['<>', 'retur_buyer_id', $excludeReturId])
            ->column();

        $grades = TrnStockGreige::gradeOptions();
        $list = [];
        foreach ($items as $item) {
            $stock = $item->stock;
            $grade = $stock !== null ? $stock->grade : null;
            $list[] = [
                'id' => $item->stock_id,
                'qr_code' => $stock !== null ? $stock->qr_code : null,
                'qty' => $item->qty,
                'grade' => $grade,
                'grade_name' => $grades[$grade] ?? '-',
                'color' => $stock !== null ? $stock->color : null,
                'returned' => in_array($item->stock_id, $returned),
                'text' => "#{$item->stock_id}"
                    . ($stock !== null && $stock->qr_code ? " | {$stock->qr_code}" : '')
                    . ' | ' . Yii::$app->formatter->asDecimal($item->qty)
                    . ' | ' . ($grades[$grade] ?? '-')
                    . ($stock !== null && $stock->color ? " | {$stock->color}" : ''),
            ];
        }
        return $list;
    }

    /**
     * Cari roll yang sudah dikirim berdasarkan QR code atau ID stock (hasil scan label).
     * @param int|null $woId null = cari di semua WO
     * @param string $code
     * @return array|null ['wo_id'=>..., 'wo_no'=>..., 'kirim_buyer_id'=>..., 'stock_id'=>..., 'qty'=>..., 'grade'=>...]
     */
    public static function findSentRoll($woId, $code)
    {
        $code = trim((string)$code);
        if ($code === '') {
            return null;
        }

        $condition = ['or', ['g.qr_code' => $code]];
        if (ctype_digit($code)) {
            $condition[] = ['g.id' => (int)$code];
        }

        $row = TrnKirimBuyerItem::find()
            ->alias('kbi')
            ->innerJoin(TrnKirimBuyer::tableName() . ' kb', 'kb.id = kbi.kirim_buyer_id')
            ->innerJoin(TrnKirimBuyerHeader::tableName() . ' h', 'h.id = kb.header_id')
            ->innerJoin(TrnGudangJadi::tableName() . ' g', 'g.id = kbi.stock_id')
            ->innerJoin(TrnWo::tableName() . ' w', 'w.id = kb.wo_id')
            ->select(['wo_id' => 'kb.wo_id', 'wo_no' => 'w.no', 'kirim_buyer_id' => 'kbi.kirim_buyer_id', 'stock_id' => 'kbi.stock_id', 'qty' => 'kbi.qty', 'grade' => 'g.grade'])
            ->where(['h.status' => TrnKirimBuyerHeader::STATUS_POSTED])
            ->andFilterWhere(['kb.wo_id' => $woId])
            ->andWhere($condition)
            ->orderBy(['h.date' => SORT_DESC])
            ->asArray()
            ->one();

        if ($row !== null) {
            return $row;
        }

        // Cari di stok yang dimutasi ke Ex-Finish jika tidak ada di pengiriman reguler
        $gjCondition = ['or', ['qr_code' => $code]];
        if (ctype_digit($code)) {
            $gjCondition[] = ['id' => (int)$code];
        }
        $gj = TrnGudangJadi::find()
            ->where($gjCondition)
            ->andFilterWhere(['wo_id' => $woId])
            ->andWhere(['status' => TrnGudangJadi::STATUS_MUTASI_EF])
            ->one();

        if ($gj !== null && $gj->wo !== null) {
            $sjEf = (new \yii\db\Query())
                ->select(['sj.id'])
                ->from(SuratJalanExFinish::tableName() . ' sj')
                ->innerJoin(JualExFinish::tableName() . ' j', 'j.id = sj.memo_id')
                ->innerJoin(JualExFinishItem::tableName() . ' ji', 'ji.jual_id = j.id')
                ->where(['ji.no_wo' => $gj->wo->no])
                ->orderBy(['sj.created_at' => SORT_DESC])
                ->one();

            if ($sjEf !== null) {
                return [
                    'wo_id' => $gj->wo_id,
                    'wo_no' => $gj->wo->no,
                    'kirim_buyer_id' => 'ef-' . $sjEf['id'],
                    'stock_id' => $gj->id,
                    'qty' => (float)$gj->qty,
                    'grade' => $gj->grade,
                ];
            }
        }

        return null;
    }
}
