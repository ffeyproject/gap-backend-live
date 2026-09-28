<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_gudang_jadi_opname_pcs".
 *
 * @property int $id
 * @property int|null $id_trn_gudang_jadi Relasi ke trn_gudang_jadi(id)
 * @property string $opname_code Nomor / Kode Dokumen Opname
 * @property string $qr_code QR Code Barang Pcs Roll
 * @property string|null $qr_code_desc Deskripsi / Spesifikasi Kain Pcs Roll
 * @property float $qty Jumlah Yard / Meter
 * @property string $unit Satuan Barang
 * @property int $grade 1=Grade A, 2=Grade B, 3=Grade C, dst
 * @property string|null $join_piece Keterangan Join Piece
 * @property string $locs_code Kode Lokasi Gudang
 * @property int $status 1=Draft/Submitted, 2=Verified
 * @property string|null $remark Catatan Tambahan
 * @property int $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnGudangJadi $gudangJadi
 */
class TrnGudangJadiOpnamePcs extends \yii\db\ActiveRecord
{
    const STATUS_STOCK = 1;
    const STATUS_DRAFT = 1; // Alias for backward compatibility
    const STATUS_VERIFIED = 2;
    const STATUS_OUT = 3;

    /**
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_STOCK => 'Stock',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_OUT => 'Out',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $options = self::statusOptions();
        return isset($options[$this->status]) ? $options[$this->status] : 'Unknown';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_gudang_jadi_opname_pcs';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['opname_code', 'qr_code', 'qty', 'unit', 'grade', 'locs_code'], 'required'],
            [['qty'], 'number'],
            [['qr_code_desc', 'remark'], 'string'],
            [['opname_code', 'qr_code'], 'string', 'max' => 100],
            [['unit'], 'string', 'max' => 20],
            [['join_piece', 'locs_code'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => self::STATUS_STOCK],
            [['status'], 'in', 'range' => [self::STATUS_STOCK, self::STATUS_VERIFIED, self::STATUS_OUT]],
            [['locs_code'], 'default', 'value' => 'TRANSIT'],
            [['unit'], 'default', 'value' => 'YARDS'],
            [['grade'], 'default', 'value' => 1],
            [['id_trn_gudang_jadi'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['id_trn_gudang_jadi' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_trn_gudang_jadi' => 'ID Gudang Jadi',
            'opname_code' => 'Kode Opname',
            'qr_code' => 'QR Code',
            'qr_code_desc' => 'Deskripsi QR Code',
            'qty' => 'Qty',
            'unit' => 'Satuan',
            'grade' => 'Grade',
            'join_piece' => 'Join Piece',
            'locs_code' => 'Kode Lokasi',
            'status' => 'Status',
            'remark' => 'Catatan',
            'created_at' => 'Dibuat Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_at' => 'Diubah Pada',
            'updated_by' => 'Diubah Oleh',
        ];
    }

    /**
     * Gets query for [[GudangJadi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGudangJadi()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'id_trn_gudang_jadi']);
    }

    private $_parsedQrData = null;
    private $_cachedWo = false;

    /**
     * Parse raw QR code string into structured data
     * @param string|null $qr
     * @param string|null $qrDesc
     * @return array
     */
    public static function parseQrData($qr, $qrDesc = null)
    {
        $result = [
            'ins_type' => null,     // 'INS', 'INS2', or 'MKL'
            'ins_id' => null,       // int inspecting header id
            'item_id' => null,      // int inspecting item id
            'k3l' => null,
            'wo_no' => null,
            'motif' => null,
            'color' => null,
            'lot' => null,
            'length' => null,
            'grade' => null,
        ];

        $sourceStr = !empty($qr) ? trim($qr) : (!empty($qrDesc) ? trim($qrDesc) : '');
        if (empty($sourceStr)) {
            return $result;
        }

        // 1. Extract prefix pattern like [INS2-50514-946687], [INS-65911-1444388] or [MKL-123-456]
        if (preg_match('/^\[(INS2|INS|MKL)-(\d+)-(\d+)\](.*)$/i', $sourceStr, $matches)) {
            $result['ins_type'] = strtoupper($matches[1]);
            $result['ins_id'] = (int)$matches[2];
            $result['item_id'] = (int)$matches[3];
            $sourceStr = $matches[4];
        } elseif (preg_match('/^(INS2|INS|MKL)-(\d+)-(\d+)$/i', $sourceStr, $matches)) {
            $result['ins_type'] = strtoupper($matches[1]);
            $result['ins_id'] = (int)$matches[2];
            $result['item_id'] = (int)$matches[3];
        }

        // If qrDesc has prefix but qr doesn't
        if (empty($result['ins_type']) && !empty($qrDesc)) {
            if (preg_match('/^\[(INS2|INS|MKL)-(\d+)-(\d+)\]/i', trim($qrDesc), $mDesc)) {
                $result['ins_type'] = strtoupper($mDesc[1]);
                $result['ins_id'] = (int)$mDesc[2];
                $result['item_id'] = (int)$mDesc[3];
            }
        }

        // Split by exclamation mark (!)
        $parts = explode('!', $sourceStr);

        // Find WO in parts: e.g. 26-D-002705, D2510/03081L, D2512/03804L, P25..., etc
        foreach ($parts as $p) {
            $p = trim($p);
            if (empty($p)) continue;
            if (preg_match('/^[DP]\d{4}\/\d{4,6}[A-Z]?$/i', $p) || preg_match('/^\d{2}-[A-Z]-\d{6}$/i', $p)) {
                $result['wo_no'] = strtoupper($p);
                break;
            }
        }

        if ($result['wo_no']) {
            $woIndex = -1;
            foreach ($parts as $idx => $p) {
                if (strtoupper(trim($p)) === $result['wo_no']) {
                    $woIndex = $idx;
                    break;
                }
            }
            if ($woIndex !== -1) {
                if (isset($parts[$woIndex + 1])) {
                    $result['motif'] = trim($parts[$woIndex + 1]);
                }
                if (isset($parts[$woIndex + 2])) {
                    $result['color'] = trim($parts[$woIndex + 2]);
                }
                if (isset($parts[$woIndex + 3])) {
                    $result['lot'] = trim($parts[$woIndex + 3]);
                }
                if (isset($parts[$woIndex + 4])) {
                    $result['length'] = trim($parts[$woIndex + 4]);
                }
                if (isset($parts[$woIndex + 5])) {
                    $result['grade'] = trim($parts[$woIndex + 5]);
                }
            }
        }

        // Also check if color in qrDesc if not parsed
        if (empty($result['color']) && !empty($qrDesc) && $qrDesc !== $qr) {
            $descParts = explode('!', $qrDesc);
            foreach ($descParts as $dp) {
                if (preg_match('/^[DP]\d{4}\/\d{4,6}[A-Z]?$/i', trim($dp)) || preg_match('/^\d{2}-[A-Z]-\d{6}$/i', trim($dp))) {
                    $wIdx = array_search($dp, $descParts);
                    if ($wIdx !== false && isset($descParts[$wIdx + 3])) {
                        $result['color'] = trim($descParts[$wIdx + 3]);
                    } elseif ($wIdx !== false && isset($descParts[$wIdx + 2])) {
                        $result['color'] = trim($descParts[$wIdx + 2]);
                    }
                    if ($wIdx !== false && isset($descParts[$wIdx + 1]) && empty($result['motif'])) {
                        $result['motif'] = trim($descParts[$wIdx + 1]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getParsedQrData()
    {
        if ($this->_parsedQrData === null) {
            $this->_parsedQrData = self::parseQrData($this->qr_code, $this->qr_code_desc);
        }
        return $this->_parsedQrData;
    }

    /**
     * Gets related TrnWo model (from gudangJadi or by inspecting / WO number parsed from QR)
     * @return TrnWo|null
     */
    public function getWo()
    {
        if ($this->_cachedWo !== false) {
            return $this->_cachedWo;
        }

        if ($this->gudangJadi && $this->gudangJadi->wo) {
            $this->_cachedWo = $this->gudangJadi->wo;
            return $this->_cachedWo;
        }

        $parsed = $this->getParsedQrData();
        if (!empty($parsed['item_id']) && !empty($parsed['ins_type'])) {
            if ($parsed['ins_type'] === 'MKL' || $parsed['ins_type'] === 'INS2') {
                $mklItem = InspectingMklBjItems::findOne($parsed['item_id']);
                if ($mklItem && $mklItem->inspecting && $mklItem->inspecting->wo) {
                    $this->_cachedWo = $mklItem->inspecting->wo;
                    return $this->_cachedWo;
                }
            } else {
                $insItem = InspectingItem::findOne($parsed['item_id']);
                if ($insItem && $insItem->inspecting && $insItem->inspecting->wo) {
                    $this->_cachedWo = $insItem->inspecting->wo;
                    return $this->_cachedWo;
                }
            }
        }

        if (!empty($parsed['wo_no'])) {
            $this->_cachedWo = TrnWo::findOne(['no' => $parsed['wo_no']]);
            return $this->_cachedWo;
        }

        $this->_cachedWo = null;
        return null;
    }

    /**
     * @return string
     */
    public function getWoNo()
    {
        $wo = $this->getWo();
        if ($wo) {
            return $wo->no;
        }
        $parsed = $this->getParsedQrData();
        return !empty($parsed['wo_no']) ? $parsed['wo_no'] : '-';
    }

    /**
     * @return string
     */
    public function getScNo()
    {
        $wo = $this->getWo();
        if ($wo && $wo->mo && $wo->mo->scGreige && $wo->mo->scGreige->sc) {
            return $wo->mo->scGreige->sc->no;
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getMarketingName()
    {
        $wo = $this->getWo();
        if ($wo && $wo->mo && $wo->mo->scGreige && $wo->mo->scGreige->sc && $wo->mo->scGreige->sc->marketing) {
            return $wo->mo->scGreige->sc->marketing->full_name;
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $wo = $this->getWo();
        if ($wo && $wo->mo && $wo->mo->scGreige && $wo->mo->scGreige->sc && $wo->mo->scGreige->sc->cust) {
            return $wo->mo->scGreige->sc->cust->name;
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getMotif()
    {
        if ($this->gudangJadi && $this->gudangJadi->wo) {
            return $this->gudangJadi->wo->greigeNamaKain;
        }
        $wo = $this->getWo();
        if ($wo) {
            return $wo->greigeNamaKain;
        }
        $parsed = $this->getParsedQrData();
        if (!empty($parsed['motif'])) {
            return $parsed['motif'];
        }
        return !empty($this->qr_code_desc) ? $this->qr_code_desc : '-';
    }

    /**
     * Ekstrak nama warna dari string No. Lot jika warna diisi di lot (contoh: ET 04 / COKLAT TUA -> COKLAT TUA)
     * @param string|null $lot
     * @return string|null
     */
    public static function extractColorFromLot($lot)
    {
        if (empty($lot) || trim($lot) === '-') {
            return null;
        }
        $lot = trim($lot);

        if (strpos($lot, '/') !== false) {
            $parts = array_map('trim', explode('/', $lot));
            for ($i = count($parts) - 1; $i >= 0; $i--) {
                $p = $parts[$i];
                $cleanP = trim(preg_replace('/^\d+\s*[\/-]?\s*/', '', $p));
                if (preg_match('/[a-zA-Z]{3,}/', $cleanP)) {
                    return $cleanP;
                }
            }
            $last = end($parts);
            if (!empty($last)) {
                return $last;
            }
        }

        return $lot;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        if ($this->gudangJadi && !empty($this->gudangJadi->color) && trim($this->gudangJadi->color) !== '-') {
            return $this->gudangJadi->color;
        }
        $parsed = $this->getParsedQrData();
        if (!empty($parsed['color']) && trim($parsed['color']) !== '-') {
            return $parsed['color'];
        }
        if (!empty($parsed['item_id'])) {
            if ($parsed['ins_type'] === 'MKL' || $parsed['ins_type'] === 'INS2') {
                $mklItem = InspectingMklBjItems::findOne($parsed['item_id']);
                if ($mklItem && $mklItem->inspecting) {
                    if (!empty($mklItem->inspecting->colorName) && trim($mklItem->inspecting->colorName) !== '-') {
                        return $mklItem->inspecting->colorName;
                    }
                    if (!empty($mklItem->inspecting->no_lot)) {
                        $lotColor = self::extractColorFromLot($mklItem->inspecting->no_lot);
                        if (!empty($lotColor)) {
                            return $lotColor;
                        }
                    }
                }
            } else {
                $insItem = InspectingItem::findOne($parsed['item_id']);
                if ($insItem && $insItem->inspecting) {
                    if (!empty($insItem->inspecting->kombinasi) && trim($insItem->inspecting->kombinasi) !== '-') {
                        return $insItem->inspecting->kombinasi;
                    }
                    if (!empty($insItem->inspecting->no_lot)) {
                        $lotColor = self::extractColorFromLot($insItem->inspecting->no_lot);
                        if (!empty($lotColor)) {
                            return $lotColor;
                        }
                    }
                }
            }
        }
        if ($this->gudangJadi) {
            $gjLot = $this->gudangJadi->getNoLot();
            if (!empty($gjLot) && $gjLot !== '-') {
                $lotColor = self::extractColorFromLot($gjLot);
                if (!empty($lotColor)) {
                    return $lotColor;
                }
            }
        }
        if (!empty($parsed['lot'])) {
            $lotColor = self::extractColorFromLot($parsed['lot']);
            if (!empty($lotColor)) {
                return $lotColor;
            }
        }
        $wo = $this->getWo();
        if ($wo && $wo->woColors) {
            foreach ($wo->woColors as $wc) {
                if ($wc->moColor && !empty($wc->moColor->color)) {
                    return $wc->moColor->color;
                }
            }
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getUnitName()
    {
        if (is_numeric($this->unit)) {
            $units = MstGreigeGroup::unitOptions();
            return isset($units[$this->unit]) ? $units[$this->unit] : $this->unit;
        }
        return $this->unit;
    }

    /**
     * @return string
     */
    public function getGradeName()
    {
        $grades = TrnStockGreige::gradeOptions();
        return isset($grades[$this->grade]) ? $grades[$this->grade] : 'Grade ' . $this->grade;
    }
}

