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
            self::STATUS_OUT => 'Out / Keluar',
        ];
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
            [['opname_code', 'qr_code', 'qty'], 'required'],
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
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
            'ins_type' => null,     // 'INS' or 'MKL'
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

        // 1. Extract prefix pattern like [INS-65911-1444388] or [MKL-123-456]
        if (preg_match('/^\[(INS|MKL)-(\d+)-(\d+)\](.*)$/i', $sourceStr, $matches)) {
            $result['ins_type'] = strtoupper($matches[1]);
            $result['ins_id'] = (int)$matches[2];
            $result['item_id'] = (int)$matches[3];
            $sourceStr = $matches[4];
        } elseif (preg_match('/^(INS|MKL)-(\d+)-(\d+)$/i', $sourceStr, $matches)) {
            $result['ins_type'] = strtoupper($matches[1]);
            $result['ins_id'] = (int)$matches[2];
            $result['item_id'] = (int)$matches[3];
        }

        // If qrDesc has prefix but qr doesn't
        if (empty($result['ins_type']) && !empty($qrDesc)) {
            if (preg_match('/^\[(INS|MKL)-(\d+)-(\d+)\]/i', trim($qrDesc), $mDesc)) {
                $result['ins_type'] = strtoupper($mDesc[1]);
                $result['ins_id'] = (int)$mDesc[2];
                $result['item_id'] = (int)$mDesc[3];
            }
        }

        // Split by exclamation mark (!)
        $parts = explode('!', $sourceStr);

        // Find WO in parts: e.g. D2510/03081L, D2512/03804L, P25..., etc
        foreach ($parts as $p) {
            $p = trim($p);
            if (empty($p)) continue;
            if (preg_match('/^[DP]\d{4}\/\d{4,6}[A-Z]?$/i', $p)) {
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
                if (preg_match('/^[DP]\d{4}\/\d{4,6}[A-Z]?$/i', trim($dp))) {
                    $wIdx = array_search($dp, $descParts);
                    if ($wIdx !== false && isset($descParts[$wIdx + 2])) {
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
            if ($parsed['ins_type'] === 'MKL') {
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
     * @return string
     */
    public function getColor()
    {
        if ($this->gudangJadi && !empty($this->gudangJadi->color)) {
            return $this->gudangJadi->color;
        }
        $parsed = $this->getParsedQrData();
        if (!empty($parsed['color'])) {
            return $parsed['color'];
        }
        if (!empty($parsed['item_id'])) {
            $insItem = InspectingItem::findOne($parsed['item_id']);
            if ($insItem && $insItem->inspecting && !empty($insItem->inspecting->kombinasi)) {
                return $insItem->inspecting->kombinasi;
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
            $unitMap = MstGreigeGroup::unitOptions();
            return isset($unitMap[(int)$this->unit]) ? $unitMap[(int)$this->unit] : (string)$this->unit;
        }
        if (!empty($this->unit)) {
            return ucfirst(strtolower($this->unit));
        }
        return 'Yard';
    }

    /**
     * @return string
     */
    public function getGradeName()
    {
        $gradeMap = TrnStockGreige::gradeOptions();
        return isset($gradeMap[$this->grade]) ? $gradeMap[$this->grade] : (string)$this->grade;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statusMap = self::statusOptions();
        return isset($statusMap[$this->status]) ? $statusMap[$this->status] : (string)$this->status;
    }

    /**
     * Sinkronkan atau buat stok di TrnGudangJadi berdasarkan data opname ini.
     * @param int|null $userId
     * @return array ['success' => bool, 'action' => 'linked'|'created'|'failed', 'message' => string, 'gj_id' => int|null]
     */
    public function syncGudangJadiStock($userId = null)
    {
        if ($userId === null) {
            $userId = (Yii::$app instanceof \yii\web\Application && !Yii::$app->user->isGuest)
                ? Yii::$app->user->id
                : ($this->created_by ?: 1);
        }

        $parsed = $this->getParsedQrData();

        // 1. Cek apakah sudah ada TrnGudangJadi yang match
        $gj = null;
        if (!empty($this->id_trn_gudang_jadi)) {
            $gj = TrnGudangJadi::findOne($this->id_trn_gudang_jadi);
        }

        if (!$gj && !empty($parsed['item_id']) && !empty($parsed['ins_type'])) {
            $gj = TrnGudangJadi::findOne(['id_from' => $parsed['item_id'], 'trans_from' => $parsed['ins_type']]);
        }

        if (!$gj) {
            $cleanQr = (!empty($parsed['ins_type']) && !empty($parsed['ins_id']) && !empty($parsed['item_id']))
                ? ($parsed['ins_type'] . '-' . $parsed['ins_id'] . '-' . $parsed['item_id'])
                : substr($this->qr_code, 0, 25);
            $gj = TrnGudangJadi::findOne(['qr_code' => $cleanQr]);
        }

        // Jika TrnGudangJadi sudah ditemukan di DB
        if ($gj) {
            $this->id_trn_gudang_jadi = $gj->id;
            $this->updated_at = time();
            $this->updated_by = $userId;
            $this->save(false);

            if (empty($gj->locs_code) && !empty($this->locs_code)) {
                $gj->locs_code = substr($this->locs_code, 0, 25);
                $gj->save(false, ['locs_code']);
            }

            return [
                'success' => true,
                'action' => 'linked',
                'gj_id' => $gj->id,
                'message' => "Data Opname #{$this->id} berhasil dihubungkan ke Stock Gudang Jadi #{$gj->id}."
            ];
        }

        // 2. Jika belum ada, buat record baru di TrnGudangJadi
        $wo = $this->getWo();
        $insItem = null;
        $mklItem = null;

        if (!empty($parsed['item_id'])) {
            if ($parsed['ins_type'] === 'MKL') {
                $mklItem = InspectingMklBjItems::findOne($parsed['item_id']);
            } else {
                $insItem = InspectingItem::findOne($parsed['item_id']);
            }
        }

        if (!$wo) {
            return [
                'success' => false,
                'action' => 'failed',
                'gj_id' => null,
                'message' => "Gagal: Nomor WO tidak dapat diidentifikasi dari QR Code ({$this->qr_code})."
            ];
        }

        $color = !empty($parsed['color']) ? $parsed['color'] : ($insItem && $insItem->inspecting ? $insItem->inspecting->kombinasi : ($mklItem && $mklItem->inspecting ? $mklItem->inspecting->kombinasi : '-'));
        $sourceRef = ($insItem && $insItem->inspecting) ? $insItem->inspecting->no : (($mklItem && $mklItem->inspecting) ? $mklItem->inspecting->no : ('Opname ' . $this->opname_code));
        $source = ($parsed['ins_type'] === 'MKL') ? TrnGudangJadi::SOURCE_MAKLOON_FINISH : TrnGudangJadi::SOURCE_PACKING;
        $grade = (int)$this->grade ?: TrnStockGreige::GRADE_A;
        $jenisGudang = ($grade == TrnStockGreige::GRADE_B) ? TrnGudangJadi::JENIS_GUDANG_GRADE_B : TrnGudangJadi::JENIS_GUDANG_LOKAL;

        $cleanQr = (!empty($parsed['ins_type']) && !empty($parsed['ins_id']) && !empty($parsed['item_id']))
            ? ($parsed['ins_type'] . '-' . $parsed['ins_id'] . '-' . $parsed['item_id'])
            : substr('OPN-' . $this->id . '-' . $this->opname_code, 0, 25);

        $unitVal = 1;
        if (is_numeric($this->unit)) {
            $unitVal = (int)$this->unit;
        } elseif ($insItem && $insItem->inspecting) {
            $unitVal = (int)$insItem->inspecting->unit;
        } elseif ($mklItem && $mklItem->inspecting) {
            $unitVal = (int)$mklItem->inspecting->unit;
        }

        $qrDesc = !empty($this->qr_code_desc) ? substr($this->qr_code_desc, 0, 255) : substr($this->qr_code, 0, 255);

        $newGj = new TrnGudangJadi([
            'jenis_gudang' => $jenisGudang,
            'wo_id' => $wo->id,
            'source' => $source,
            'source_ref' => substr($sourceRef, 0, 255),
            'unit' => $unitVal,
            'qty' => (float)$this->qty,
            'date' => date('Y-m-d'),
            'status' => TrnGudangJadi::STATUS_STOCK,
            'note' => 'Dibuat otomatis dari Stok Opname ' . $this->opname_code,
            'color' => substr($color, 0, 255),
            'grade' => $grade,
            'locs_code' => substr($this->locs_code ?: 'TRANSIT', 0, 25),
            'trans_from' => $parsed['ins_type'] ?: 'INS',
            'id_from' => $parsed['item_id'] ?: null,
            'qr_code' => $cleanQr,
            'qr_code_desc' => $qrDesc,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $newGj->detachBehaviors();
        $newGj->created_at = time();
        $newGj->updated_at = time();
        $newGj->created_by = $userId;
        $newGj->updated_by = $userId;

        if (!$newGj->save(false)) {
            return [
                'success' => false,
                'action' => 'failed',
                'gj_id' => null,
                'message' => "Gagal menyimpan record baru di Gudang Jadi untuk Opname #{$this->id}."
            ];
        }

        $this->id_trn_gudang_jadi = $newGj->id;
        $this->updated_at = time();
        $this->updated_by = $userId;
        $this->save(false);

        return [
            'success' => true,
            'action' => 'created',
            'gj_id' => $newGj->id,
            'message' => "Stock Gudang Jadi #{$newGj->id} berhasil dibuat & dihubungkan ke Opname #{$this->id}."
        ];
    }
}

