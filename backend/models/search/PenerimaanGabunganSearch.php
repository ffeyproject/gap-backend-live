<?php

namespace backend\models\search;

use backend\components\PenerimaanGudangJadiHelper;
use common\models\ar\InspectingMklBj;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnScGreige;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * Search model combining received Packing (trn_inspecting) and Packing MKL BJ (inspecting_mkl_bj),
 * including partial receipts stored in trn_gudang_jadi.
 */
class PenerimaanGabunganSearch extends Model
{
    const SOURCE_PACKING = 'PACKING';
    const SOURCE_MKL_BJ = 'MKL_BJ';

    public $source;
    public $id;
    public $jenisFilter;
    public $tglKirimRange;
    public $tglInspeksiRange;
    public $deliveredAtRange;
    public $no;
    public $wo_no;
    public $kombinasi;
    public $no_lot;
    public $kartu_proses_no;
    public $unit;

    public $from_tgl_kirim;
    public $to_tgl_kirim;
    public $from_delivered_at;
    public $to_delivered_at;
    public $hasValidDateRange = false;
    public $dateRangeError = null;

    /**
     * @return array
     */
    public static function sourceOptions()
    {
        return [
            self::SOURCE_PACKING => 'Packing',
            self::SOURCE_MKL_BJ => 'Packing MKL BJ',
        ];
    }

    /**
     * @return array
     */
    public static function jenisFilterOptions()
    {
        $options = [];
        foreach (TrnScGreige::processOptions() as $key => $label) {
            $options['P:' . $key] = 'Packing - ' . $label;
        }
        foreach (InspectingMklBj::jenisOptions() as $key => $label) {
            $options['M:' . $key] = 'MKL BJ - ' . $label;
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'unit'], 'integer'],
            [['source', 'jenisFilter', 'tglKirimRange', 'tglInspeksiRange', 'deliveredAtRange', 'no', 'wo_no', 'kombinasi', 'no_lot', 'kartu_proses_no'], 'safe'],
        ];
    }

    /**
     * Builds the unified data provider.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);

        $validation = PenerimaanGudangJadiHelper::validateEitherDateRange($this->tglKirimRange, $this->deliveredAtRange);
        $this->hasValidDateRange = $validation['hasValidDateRange'];
        $this->dateRangeError = $validation['error'];
        $this->from_tgl_kirim = $validation['kirim']['from'];
        $this->to_tgl_kirim = $validation['kirim']['to'];
        $this->from_delivered_at = $validation['penerimaan']['from'];
        $this->to_delivered_at = $validation['penerimaan']['to'];

        $outerQuery = new Query();
        $dataProvider = new ActiveDataProvider([
            'query' => $outerQuery,
            'key' => function ($model) {
                return $model['source'] . '-' . $model['id'];
            },
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [10, 200],
            ],
            'sort' => [
                'defaultOrder' => ['tgl_kirim' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [
                    'source',
                    'id',
                    'tgl_kirim',
                    'tgl_inspeksi',
                    'delivered_at',
                    'deliveredAtRange' => [
                        'asc' => ['delivered_at' => SORT_ASC],
                        'desc' => ['delivered_at' => SORT_DESC],
                    ],
                    'no',
                    'wo_no',
                    'kombinasi',
                    'no_lot',
                    'kartu_proses_no',
                    'unit',
                ],
            ],
        ]);

        if (!$this->validate() || !$this->hasValidDateRange) {
            $outerQuery->from(['ti' => 'trn_inspecting'])->where('0=1');
            return $dataProvider;
        }

        $packingQuery = (new Query())
            ->select([
                new Expression("'PACKING'::text AS source"),
                'ti.id AS id',
                'ti.no AS no',
                'ti.date AS tgl_kirim',
                'ti.tanggal_inspeksi AS tgl_inspeksi',
                'ti.delivered_at AS delivered_at',
                'ti.status AS doc_status',
                'ti.wo_id AS wo_id',
                'w.no AS wo_no',
                'ti.kombinasi AS kombinasi',
                'ti.no_lot AS no_lot',
                new Expression('COALESCE(kpd.no, kpp.no) AS kartu_proses_no'),
                new Expression('COALESCE(scg.process, ti.jenis_process)::int AS jenis_code'),
                'ti.unit AS unit',
            ])
            ->from(['ti' => TrnInspecting::tableName()])
            ->leftJoin(['w' => 'trn_wo'], 'w.id = ti.wo_id')
            ->leftJoin(['kpd' => 'trn_kartu_proses_dyeing'], 'kpd.id = ti.kartu_process_dyeing_id')
            ->leftJoin(['kpp' => 'trn_kartu_proses_printing'], 'kpp.id = ti.kartu_process_printing_id')
            ->leftJoin(['mr' => 'trn_memo_repair'], 'mr.id = ti.memo_repair_id')
            ->leftJoin(['scg' => 'trn_sc_greige'], 'scg.id = COALESCE(kpd.sc_greige_id, kpp.sc_greige_id, mr.sc_greige_id, ti.sc_greige_id)');

        PenerimaanGudangJadiHelper::applyReceiptFilter($packingQuery, self::SOURCE_PACKING, 'ti', $this->from_delivered_at, $this->to_delivered_at);
        if ($this->from_tgl_kirim !== null && $this->to_tgl_kirim !== null) {
            $packingQuery->andWhere(['between', 'ti.date', $this->from_tgl_kirim, $this->to_tgl_kirim]);
        }

        $mklQuery = (new Query())
            ->select([
                new Expression("'MKL_BJ'::text AS source"),
                'mkl.id AS id',
                'mkl.no AS no',
                'mkl.tgl_kirim AS tgl_kirim',
                'mkl.tgl_inspeksi AS tgl_inspeksi',
                'mkl.delivered_at AS delivered_at',
                'mkl.status AS doc_status',
                'mkl.wo_id AS wo_id',
                'w.no AS wo_no',
                'mc.color AS kombinasi',
                'mkl.no_lot AS no_lot',
                new Expression('NULL::varchar AS kartu_proses_no'),
                new Expression('mkl.jenis::int AS jenis_code'),
                'mkl.satuan AS unit',
            ])
            ->from(['mkl' => InspectingMklBj::tableName()])
            ->leftJoin(['w' => 'trn_wo'], 'w.id = mkl.wo_id')
            ->leftJoin(['wc' => 'trn_wo_color'], 'wc.id = mkl.wo_color_id')
            ->leftJoin(['mc' => 'trn_mo_color'], 'mc.id = wc.mo_color_id');

        PenerimaanGudangJadiHelper::applyReceiptFilter($mklQuery, self::SOURCE_MKL_BJ, 'mkl', $this->from_delivered_at, $this->to_delivered_at);
        if ($this->from_tgl_kirim !== null && $this->to_tgl_kirim !== null) {
            $mklQuery->andWhere(['between', 'mkl.tgl_kirim', $this->from_tgl_kirim, $this->to_tgl_kirim]);
        }

        if ($this->source === self::SOURCE_PACKING) {
            $baseQuery = $packingQuery;
        } elseif ($this->source === self::SOURCE_MKL_BJ) {
            $baseQuery = $mklQuery;
        } else {
            $baseQuery = $packingQuery->union($mklQuery, true);
        }

        $outerQuery->from(['u' => $baseQuery]);
        $this->applyOuterFilters($outerQuery);
        PenerimaanGudangJadiHelper::applyGradeSortAttributes($dataProvider->sort, 'COMBINED', 'u', $this->from_delivered_at, $this->to_delivered_at);

        return $dataProvider;
    }

    /**
     * Builds a single-source query with all active search filters applied (avoids UNION ALL in summary queries).
     *
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @return Query|null
     */
    public function buildSummarySourceQuery($type)
    {
        if (!$this->hasValidDateRange) {
            return null;
        }

        if ($type === self::SOURCE_PACKING) {
            if ($this->source === self::SOURCE_MKL_BJ) {
                return null;
            }
            $sourceQuery = (new Query())
                ->select([
                    new Expression("'PACKING'::text AS source"),
                    'ti.id AS id',
                    'ti.no AS no',
                    'ti.date AS tgl_kirim',
                    'ti.tanggal_inspeksi AS tgl_inspeksi',
                    'ti.delivered_at AS delivered_at',
                    'ti.status AS doc_status',
                    'w.no AS wo_no',
                    'ti.kombinasi AS kombinasi',
                    'ti.no_lot AS no_lot',
                    new Expression('COALESCE(kpd.no, kpp.no) AS kartu_proses_no'),
                    new Expression('COALESCE(scg.process, ti.jenis_process)::int AS jenis_code'),
                    'ti.unit AS unit',
                ])
                ->from(['ti' => TrnInspecting::tableName()])
                ->leftJoin(['w' => 'trn_wo'], 'w.id = ti.wo_id')
                ->leftJoin(['kpd' => 'trn_kartu_proses_dyeing'], 'kpd.id = ti.kartu_process_dyeing_id')
                ->leftJoin(['kpp' => 'trn_kartu_proses_printing'], 'kpp.id = ti.kartu_process_printing_id')
                ->leftJoin(['mr' => 'trn_memo_repair'], 'mr.id = ti.memo_repair_id')
                ->leftJoin(['scg' => 'trn_sc_greige'], 'scg.id = COALESCE(kpd.sc_greige_id, kpp.sc_greige_id, mr.sc_greige_id, ti.sc_greige_id)');
            PenerimaanGudangJadiHelper::applyReceiptFilter($sourceQuery, self::SOURCE_PACKING, 'ti', $this->from_delivered_at, $this->to_delivered_at);
            if ($this->from_tgl_kirim !== null && $this->to_tgl_kirim !== null) {
                $sourceQuery->andWhere(['between', 'ti.date', $this->from_tgl_kirim, $this->to_tgl_kirim]);
            }
        } else {
            if ($this->source === self::SOURCE_PACKING) {
                return null;
            }
            $sourceQuery = (new Query())
                ->select([
                    new Expression("'MKL_BJ'::text AS source"),
                    'mkl.id AS id',
                    'mkl.no AS no',
                    'mkl.tgl_kirim AS tgl_kirim',
                    'mkl.tgl_inspeksi AS tgl_inspeksi',
                    'mkl.delivered_at AS delivered_at',
                    'mkl.status AS doc_status',
                    'w.no AS wo_no',
                    'mc.color AS kombinasi',
                    'mkl.no_lot AS no_lot',
                    new Expression('NULL::varchar AS kartu_proses_no'),
                    new Expression('mkl.jenis::int AS jenis_code'),
                    'mkl.satuan AS unit',
                ])
                ->from(['mkl' => InspectingMklBj::tableName()])
                ->leftJoin(['w' => 'trn_wo'], 'w.id = mkl.wo_id')
                ->leftJoin(['wc' => 'trn_wo_color'], 'wc.id = mkl.wo_color_id')
                ->leftJoin(['mc' => 'trn_mo_color'], 'mc.id = wc.mo_color_id');
            PenerimaanGudangJadiHelper::applyReceiptFilter($sourceQuery, self::SOURCE_MKL_BJ, 'mkl', $this->from_delivered_at, $this->to_delivered_at);
            if ($this->from_tgl_kirim !== null && $this->to_tgl_kirim !== null) {
                $sourceQuery->andWhere(['between', 'mkl.tgl_kirim', $this->from_tgl_kirim, $this->to_tgl_kirim]);
            }
        }

        $q = (new Query())
            ->select(['u.id', 'u.unit', 'u.doc_status', 'u.delivered_at'])
            ->from(['u' => $sourceQuery]);
        $this->applyOuterFilters($q);
        return $q;
    }

    /**
     * Applies outer grid filters to a query aliased as `u`.
     *
     * @param Query $outerQuery
     * @return void
     */
    protected function applyOuterFilters($outerQuery)
    {
        $outerQuery->andFilterWhere([
            'u.id' => $this->id,
            'u.unit' => $this->unit,
        ]);

        if (!empty($this->jenisFilter) && strpos($this->jenisFilter, ':') !== false) {
            list($jenisPrefix, $jenisCode) = explode(':', $this->jenisFilter, 2);
            if ($jenisPrefix === 'P') {
                $outerQuery->andWhere(['u.source' => self::SOURCE_PACKING, 'u.jenis_code' => (int)$jenisCode]);
            } elseif ($jenisPrefix === 'M') {
                $outerQuery->andWhere(['u.source' => self::SOURCE_MKL_BJ, 'u.jenis_code' => (int)$jenisCode]);
            }
        }

        if (!empty($this->tglInspeksiRange)) {
            $separator = strpos($this->tglInspeksiRange, ' to ') !== false ? ' to ' : ' - ';
            $dates = explode($separator, $this->tglInspeksiRange);
            $fromInsp = isset($dates[0]) ? trim($dates[0]) : null;
            $toInsp = isset($dates[1]) ? trim($dates[1]) : $fromInsp;

            if ($fromInsp && $toInsp) {
                $outerQuery->andFilterWhere(['between', 'u.tgl_inspeksi', $fromInsp, $toInsp]);
            }
        }

        $outerQuery->andFilterWhere(['ilike', 'u.no', $this->no])
            ->andFilterWhere(['ilike', 'u.wo_no', $this->wo_no])
            ->andFilterWhere(['ilike', 'u.kombinasi', $this->kombinasi])
            ->andFilterWhere(['ilike', 'u.no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'u.kartu_proses_no', $this->kartu_proses_no]);
    }
}
