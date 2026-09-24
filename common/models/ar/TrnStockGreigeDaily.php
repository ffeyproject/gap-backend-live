<?php

namespace common\models\ar;

use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "trn_stock_greige_daily".
 *
 * @property int $id
 * @property string $date
 * @property int $greige_id
 * @property int $greige_group_id
 * @property int $asal_greige 1=Water Jet Loom, 2=Beli Lokal, 3=Rapier, 4=Beli Import, 5=Lain-lain, 6=Retur, 7=Mutasi, 8=Pemotongan, 9=Hasil Makloon
 * @property float $total_panjang
 * @property int $total_roll
 * @property float|null $grade_a
 * @property float|null $grade_b
 * @property float|null $grade_c
 * @property float|null $grade_d
 * @property float|null $grade_e
 * @property float|null $grade_ng
 * @property float|null $grade_lain
 * @property string|null $note
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property User $createdBy
 * @property User $updatedBy
 *
 * Virtual / computed properties:
 * @property float $prev_total_panjang
 * @property int $prev_total_roll
 * @property string|null $prev_date
 * @property float $diff_panjang
 * @property int $diff_roll
 */
class TrnStockGreigeDaily extends ActiveRecord
{
    // Virtual attributes when querying with window functions / calculations
    public $prev_total_panjang = 0;
    public $prev_total_roll = 0;
    public $prev_date = null;
    public $diff_panjang = 0;
    public $diff_roll = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_stock_greige_daily';
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
            [['date', 'greige_id', 'greige_group_id'], 'required'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['greige_id', 'greige_group_id', 'asal_greige', 'total_roll', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['asal_greige'], 'default', 'value' => TrnStockGreige::ASAL_GREIGE_WJL],
            [['greige_id', 'greige_group_id', 'asal_greige', 'total_roll', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['total_panjang', 'grade_a', 'grade_b', 'grade_c', 'grade_d', 'grade_e', 'grade_ng', 'grade_lain'], 'number'],
            [['total_panjang', 'total_roll'], 'default', 'value' => 0],
            [['note'], 'string'],
            [['date', 'greige_id', 'asal_greige'], 'unique', 'targetAttribute' => ['date', 'greige_id', 'asal_greige'], 'message' => 'Data stock motif dan asal greige pada tanggal ini sudah ada.'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::class, 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::class, 'targetAttribute' => ['greige_group_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Tanggal Stock',
            'greige_id' => 'Motif / Greige',
            'greige_group_id' => 'Group Greige',
            'asal_greige' => 'Asal Greige',
            'total_panjang' => 'Total Stock (m)',
            'total_roll' => 'Total Roll',
            'grade_a' => 'Grade A (m)',
            'grade_b' => 'Grade B (m)',
            'grade_c' => 'Grade C (m)',
            'grade_d' => 'Grade D (m)',
            'grade_e' => 'Grade E (m)',
            'grade_ng' => 'Grade NG (m)',
            'grade_lain' => 'Grade Lain (m)',
            'note' => 'Catatan',
            'created_at' => 'Waktu Simpan',
            'created_by' => 'Disimpan Oleh',
            'updated_at' => 'Terakhir Update',
            'updated_by' => 'Diupdate Oleh',
            'prev_total_panjang' => 'Stock Sebelumnya (m)',
            'prev_total_roll' => 'Roll Sebelumnya',
            'diff_panjang' => 'Tambahan / Selisih (m)',
            'diff_roll' => 'Tambahan / Selisih Roll',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::class, ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::class, ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Asal Greige Label
     * @return string
     */
    public function getAsalGreigeName()
    {
        return TrnStockGreige::asalGreigeOptions()[$this->asal_greige] ?? '-';
    }

    /**
     * Format tanggal dengan nama hari Indonesia (Contoh: Kamis, 24 Sep 2026)
     * @param string $dateStr
     * @param bool $withDay
     * @return string
     */
    public static function formatIndonesianDate($dateStr, $withDay = true)
    {
        if (empty($dateStr)) return '-';
        $timestamp = strtotime($dateStr);
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $dayName = $days[date('l', $timestamp)] ?? '';
        $d = date('d', $timestamp);
        $m = $months[(int)date('n', $timestamp)] ?? '';
        $y = date('Y', $timestamp);

        if ($withDay) {
            return "{$dayName}, {$d} {$m} {$y}";
        }
        return "{$d} {$m} {$y}";
    }

    /**
     * Mengambil nama hari bahasa Indonesia (Senin, Selasa, dll.)
     * @param string $dateStr
     * @return string
     */
    public static function getIndonesianDayName($dateStr)
    {
        if (empty($dateStr)) return '';
        $timestamp = strtotime($dateStr);
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        return $days[date('l', $timestamp)] ?? '';
    }

    /**
     * Getter nama hari untuk model saat ini
     * @return string
     */
    public function getDayName()
    {
        return self::getIndonesianDayName($this->date);
    }

    /**
     * Getter tanggal lengkap dengan nama hari
     * @return string
     */
    public function getFormattedDate()
    {
        return self::formatIndonesianDate($this->date, true);
    }

    /**
     * Nama Kain / Motif
     * @return string
     */
    public function getGreigeNamaKain()
    {
        return $this->greige ? $this->greige->nama_kain : '-';
    }

    /**
     * Snapshot stock greige dari gudang fresh (status valid) untuk tanggal tertentu
     *
     * @param string|null $date Format: Y-m-d (default: hari ini)
     * @return array
     * @throws Exception
     */
    public static function snapshotStock($date = null)
    {
        if ($date === null || empty($date)) {
            $date = date('Y-m-d');
        }

        // Ambil data agregat stock fresh valid dari trn_stock_greige per motif dan per asal_greige
        $query = (new Query())
            ->select([
                'greige_id' => 'tsg.greige_id',
                'greige_group_id' => 'tsg.greige_group_id',
                'asal_greige' => 'tsg.asal_greige',
                'total_panjang' => new Expression('COALESCE(SUM(tsg.panjang_m), 0)'),
                'total_roll' => new Expression('COUNT(tsg.id)'),
                'grade_a' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_A . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_b' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_B . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_c' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_C . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_d' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_D . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_e' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_E . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_ng' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade = ' . TrnStockGreige::GRADE_NG . ' THEN tsg.panjang_m ELSE 0 END), 0)'),
                'grade_lain' => new Expression('COALESCE(SUM(CASE WHEN tsg.grade NOT IN (' . implode(',', [
                    TrnStockGreige::GRADE_A,
                    TrnStockGreige::GRADE_B,
                    TrnStockGreige::GRADE_C,
                    TrnStockGreige::GRADE_D,
                    TrnStockGreige::GRADE_E,
                    TrnStockGreige::GRADE_NG,
                ]) . ') THEN tsg.panjang_m ELSE 0 END), 0)'),
            ])
            ->from(['tsg' => TrnStockGreige::tableName()])
            ->where([
                'tsg.jenis_gudang' => TrnStockGreige::JG_FRESH,
                'tsg.status' => TrnStockGreige::STATUS_VALID,
            ])
            ->groupBy(['tsg.greige_id', 'tsg.greige_group_id', 'tsg.asal_greige']);

        $rows = $query->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $savedCount = 0;
            $updatedCount = 0;
            $grandTotalM = 0;
            $grandTotalRoll = 0;

            foreach ($rows as $row) {
                $greigeId = (int)$row['greige_id'];
                $greigeGroupId = (int)$row['greige_group_id'];
                $asalGreige = (int)$row['asal_greige'];

                $model = self::findOne(['date' => $date, 'greige_id' => $greigeId, 'asal_greige' => $asalGreige]);
                $isNew = false;
                if ($model === null) {
                    $model = new self();
                    $model->date = $date;
                    $model->greige_id = $greigeId;
                    $model->greige_group_id = $greigeGroupId;
                    $model->asal_greige = $asalGreige;
                    $isNew = true;
                } else {
                    $model->greige_group_id = $greigeGroupId;
                }

                $model->total_panjang = (float)$row['total_panjang'];
                $model->total_roll = (int)$row['total_roll'];
                $model->grade_a = (float)$row['grade_a'];
                $model->grade_b = (float)$row['grade_b'];
                $model->grade_c = (float)$row['grade_c'];
                $model->grade_d = (float)$row['grade_d'];
                $model->grade_e = (float)$row['grade_e'];
                $model->grade_ng = (float)$row['grade_ng'];
                $model->grade_lain = (float)$row['grade_lain'];

                if (!$model->save()) {
                    throw new Exception('Gagal menyimpan snapshot: ' . json_encode($model->errors));
                }

                if ($isNew) {
                    $savedCount++;
                } else {
                    $updatedCount++;
                }

                $grandTotalM += (float)$row['total_panjang'];
                $grandTotalRoll += (int)$row['total_roll'];
            }

            $transaction->commit();

            return [
                'success' => true,
                'date' => $date,
                'total_motifs' => count($rows),
                'new_saved' => $savedCount,
                'updated' => $updatedCount,
                'grand_total_m' => $grandTotalM,
                'grand_total_roll' => $grandTotalRoll,
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
