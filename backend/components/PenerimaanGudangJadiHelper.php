<?php

namespace backend\components;

use common\models\ar\InspectingItem;
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnInspecting;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Html;

/**
 * Shared helper for querying, summarizing, and computing accurate received totals
 * (including partial receipts) from trn_gudang_jadi with legacy fallback for pre-2023 records.
 */
class PenerimaanGudangJadiHelper
{
    const MAX_RANGE_DAYS = 38;

    /**
     * Validates a single date range string (`Y-m-d to Y-m-d` or `Y-m-d - Y-m-d`).
     *
     * @param string|null $rangeStr
     * @param string $fieldLabel
     * @param int $maxDays
     * @return array
     */
    public static function validateDateRange($rangeStr, $fieldLabel, $maxDays = self::MAX_RANGE_DAYS)
    {
        if (empty($rangeStr)) {
            return [
                'filled' => false,
                'valid' => false,
                'from' => null,
                'to' => null,
                'error' => null,
            ];
        }

        $separator = strpos($rangeStr, ' to ') !== false ? ' to ' : ' - ';
        $parts = explode($separator, $rangeStr);
        $startStr = isset($parts[0]) ? trim($parts[0]) : '';
        $endStr = isset($parts[1]) ? trim($parts[1]) : $startStr;

        $startDate = \DateTime::createFromFormat('Y-m-d', $startStr);
        $endDate = \DateTime::createFromFormat('Y-m-d', $endStr);

        $validFormat = $startDate && $endDate
            && $startDate->format('Y-m-d') === $startStr
            && $endDate->format('Y-m-d') === $endStr;

        if ($validFormat && $startDate <= $endDate && $startDate->diff($endDate)->days <= $maxDays) {
            return [
                'filled' => true,
                'valid' => true,
                'from' => $startStr,
                'to' => $endStr,
                'error' => null,
            ];
        }

        return [
            'filled' => true,
            'valid' => false,
            'from' => null,
            'to' => null,
            'error' => "Rentang {$fieldLabel} tidak valid atau melebihi maksimal 1 bulan 1 minggu ({$maxDays} hari) dari tanggal pertama yang dipilih.",
        ];
    }

    /**
     * Validates that at least one of Tanggal Kirim or Tanggal Penerimaan is filled and all filled ranges are valid.
     *
     * @param string|null $tglKirimRange
     * @param string|null $deliveredAtRange
     * @param int $maxDays
     * @return array
     */
    public static function validateEitherDateRange($tglKirimRange, $deliveredAtRange, $maxDays = self::MAX_RANGE_DAYS)
    {
        $kirim = self::validateDateRange($tglKirimRange, 'Tanggal Kirim', $maxDays);
        $penerimaan = self::validateDateRange($deliveredAtRange, 'Tanggal Penerimaan', $maxDays);

        if (!$kirim['filled'] && !$penerimaan['filled']) {
            return [
                'hasValidDateRange' => false,
                'error' => null,
                'kirim' => $kirim,
                'penerimaan' => $penerimaan,
            ];
        }

        $errors = array_filter([$kirim['error'], $penerimaan['error']]);
        if (!empty($errors)) {
            return [
                'hasValidDateRange' => false,
                'error' => implode(' ', $errors),
                'kirim' => $kirim,
                'penerimaan' => $penerimaan,
            ];
        }

        return [
            'hasValidDateRange' => true,
            'error' => null,
            'kirim' => $kirim,
            'penerimaan' => $penerimaan,
        ];
    }

    /**
     * Returns metadata for Packing (`INS`) or MKL BJ (`MKL`).
     *
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @return array
     */
    public static function getSourceConfig($type)
    {
        if ($type === 'PACKING') {
            return [
                'headerTable' => TrnInspecting::tableName(),
                'itemTable' => InspectingItem::tableName(),
                'transFrom' => 'INS',
                'deliveredStatus' => TrnInspecting::STATUS_DELIVERED,
                'allowedStatuses' => [
                    TrnInspecting::STATUS_DELIVERED,
                    TrnInspecting::STATUS_APPROVED,
                    TrnInspecting::STATUS_APPROVED_PARTIAL,
                ],
                'unitColumn' => 'unit',
            ];
        }

        return [
            'headerTable' => InspectingMklBj::tableName(),
            'itemTable' => InspectingMklBjItems::tableName(),
            'transFrom' => 'MKL',
            'deliveredStatus' => InspectingMklBj::STATUS_DELIVERED,
            'allowedStatuses' => [
                InspectingMklBj::STATUS_DELIVERED,
                InspectingMklBj::STATUS_POSTED,
                InspectingMklBj::STATUS_POSTED_PARTIAL,
            ],
            'unitColumn' => 'satuan',
        ];
    }

    /**
     * Applies receipt eligibility and Tanggal Penerimaan filtering on a query for Packing or MKL BJ.
     *
     * @param Query $query
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @param string $headerAlias Table alias in $query (e.g. 'trn_inspecting', 'ti', 'mkl')
     * @param string|null $fromDeliv Y-m-d
     * @param string|null $toDeliv Y-m-d
     * @return void
     */
    public static function applyReceiptFilter($query, $type, $headerAlias, $fromDeliv = null, $toDeliv = null)
    {
        $cfg = self::getSourceConfig($type);
        $itemTable = $cfg['itemTable'];
        $gjTable = TrnGudangJadi::tableName();
        $transFrom = $cfg['transFrom'];
        $tfParam = ':tf_' . strtolower($transFrom);

        $query->andWhere(['in', "{$headerAlias}.status", $cfg['allowedStatuses']]);

        $anyGjSubQuery = (new Query())
            ->select(new Expression('1'))
            ->from(['ii_chk' => $itemTable])
            ->innerJoin(['gj_chk' => $gjTable], "gj_chk.trans_from = {$tfParam} AND gj_chk.id_from = ii_chk.id", [$tfParam => $transFrom])
            ->where("ii_chk.inspecting_id = {$headerAlias}.id");

        if ($fromDeliv !== null && $toDeliv !== null) {
            $tfFilterParam = ':tff_' . strtolower($transFrom);
            // Use the indexed (trans_from, date) lookup to find matching header IDs fast.
            $matchedHeaderIds = (new Query())
                ->select('ii_f.inspecting_id')
                ->distinct()
                ->from(['ii_f' => $itemTable])
                ->innerJoin(['gj_f' => $gjTable], "gj_f.trans_from = {$tfFilterParam} AND gj_f.id_from = ii_f.id", [$tfFilterParam => $transFrom])
                ->where(['between', 'gj_f.date', $fromDeliv, $toDeliv]);

            if ($fromDeliv >= '2023-07-01') {
                // From mid-2023 onward all real receipts have linked rows in trn_gudang_jadi;
                // exclude legacy unlinked docs whose delivered_at was later backfilled by SyncStatus.
                $query->andWhere(['in', "{$headerAlias}.id", $matchedHeaderIds]);
            } else {
                $startTs = strtotime($fromDeliv . ' 00:00:00');
                $endTs = strtotime($toDeliv . ' 23:59:59');

                $query->andWhere([
                    'or',
                    ['in', "{$headerAlias}.id", $matchedHeaderIds],
                    [
                        'and',
                        ["{$headerAlias}.status" => $cfg['deliveredStatus']],
                        ['between', "{$headerAlias}.delivered_at", $startTs, $endTs],
                        ['not exists', $anyGjSubQuery],
                    ],
                ]);
            }
        } else {
            $query->andWhere([
                'or',
                ["{$headerAlias}.status" => $cfg['deliveredStatus']],
                ['exists', $anyGjSubQuery],
            ]);
        }
    }

    /**
     * Aggregates overall received totals across ALL matching search results grouped by unit and grade.
     *
     * @param Query $docSubQuery Must select columns `id`, `unit`, and `doc_status`
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @param string|null $fromDeliv
     * @param string|null $toDeliv
     * @param array $summaryByUnit Reference to accumulator array
     * @return void
     */
    public static function accumulateSummary($docSubQuery, $type, $fromDeliv, $toDeliv, &$summaryByUnit)
    {
        $docRows = (clone $docSubQuery)->all();
        if (empty($docRows)) {
            return;
        }

        $cfg = self::getSourceConfig($type);
        $itemTable = $cfg['itemTable'];
        $gjTable = TrnGudangJadi::tableName();
        $transFrom = $cfg['transFrom'];
        $tfParam = ':tfs_' . strtolower($transFrom);
        $unitOptions = MstGreigeGroup::unitOptions();

        $docUnitMap = [];
        $docStatusMap = [];
        $docIds = [];
        foreach ($docRows as $d) {
            $id = (int)$d['id'];
            $docIds[] = $id;
            $docUnitMap[$id] = (int)$d['unit'];
            $docStatusMap[$id] = isset($d['doc_status']) ? (int)$d['doc_status'] : $cfg['deliveredStatus'];
        }
        $docIds = array_values(array_unique($docIds));

        $addEntry = function ($unitId, $grade, $qty) use (&$summaryByUnit, $unitOptions) {
            if (!isset($summaryByUnit[$unitId])) {
                $summaryByUnit[$unitId] = [
                    'unit' => $unitId,
                    'unitLabel' => isset($unitOptions[$unitId]) ? $unitOptions[$unitId] : 'Unit #' . $unitId,
                    'total' => 0,
                    'grades' => [],
                ];
            }
            $summaryByUnit[$unitId]['grades'][$grade] = (isset($summaryByUnit[$unitId]['grades'][$grade]) ? $summaryByUnit[$unitId]['grades'][$grade] : 0) + $qty;
            $summaryByUnit[$unitId]['total'] += $qty;
        };

        // 1. Linked items actually received into trn_gudang_jadi (chunked to stay well under bind parameter limits)
        $hasGjIds = [];
        foreach (array_chunk($docIds, 2000) as $idChunk) {
            $gjQuery = (new Query())
                ->select([
                    'ii.inspecting_id',
                    'gj.grade',
                    'SUM(gj.qty) AS total_qty',
                ])
                ->from(['ii' => $itemTable])
                ->innerJoin(['gj' => $gjTable], "gj.trans_from = {$tfParam} AND gj.id_from = ii.id", [$tfParam => $transFrom])
                ->where(['ii.inspecting_id' => $idChunk])
                ->groupBy(['ii.inspecting_id', 'gj.grade']);

            if ($fromDeliv !== null && $toDeliv !== null) {
                $gjQuery->andWhere(['between', 'gj.date', $fromDeliv, $toDeliv]);
            }

            foreach ($gjQuery->all() as $row) {
                $id = (int)$row['inspecting_id'];
                $hasGjIds[$id] = true;
                $addEntry($docUnitMap[$id], (int)$row['grade'], (float)$row['total_qty']);
            }
        }

        // 2. Fallback only for pre-2023 STATUS_DELIVERED docs that had zero rows in trn_gudang_jadi
        $missingDeliveredIds = [];
        foreach ($docIds as $id) {
            if (!isset($hasGjIds[$id]) && $docStatusMap[$id] === $cfg['deliveredStatus']) {
                $missingDeliveredIds[] = $id;
            }
        }

        if (!empty($missingDeliveredIds)) {
            $tfParam2 = ':tfs2_' . strtolower($transFrom);
            $anyGjSub = (new Query())
                ->select(new Expression('1'))
                ->from(['ii2' => $itemTable])
                ->innerJoin(['gj2' => $gjTable], "gj2.trans_from = {$tfParam2} AND gj2.id_from = ii2.id", [$tfParam2 => $transFrom])
                ->where('ii2.inspecting_id = ii.inspecting_id');

            foreach (array_chunk($missingDeliveredIds, 2000) as $idChunk) {
                $legacyQuery = (new Query())
                    ->select([
                        'ii.inspecting_id',
                        'ii.grade',
                        'SUM(ii.qty) AS total_qty',
                    ])
                    ->from(['ii' => $itemTable])
                    ->where(['ii.inspecting_id' => $idChunk])
                    ->andWhere(['not exists', $anyGjSub])
                    ->groupBy(['ii.inspecting_id', 'ii.grade']);

                foreach ($legacyQuery->all() as $row) {
                    $id = (int)$row['inspecting_id'];
                    $addEntry($docUnitMap[$id], (int)$row['grade'], (float)$row['total_qty']);
                }
            }
        }
    }

    /**
     * Computes page-level per-grade totals, overall received total, total inspecting qty,
     * partial status, and receipt date(s) for a list of header IDs.
     *
     * @param int[] $ids
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @param string $keyPrefix Prefix for output maps (e.g. '' or 'PACKING_' / 'MKL_BJ_')
     * @param string|null $fromDeliv
     * @param string|null $toDeliv
     * @param array $statusMap [id => int status]
     * @param array $deliveredAtMap [id => int|null delivered_at]
     * @return array ['gradeTotals' => ..., 'rowTotals' => ..., 'inspectingTotals' => ..., 'isPartialMap' => ..., 'receiptDates' => ...]
     */
    public static function computePageDetails($ids, $type, $keyPrefix = '', $fromDeliv = null, $toDeliv = null, $statusMap = [], $deliveredAtMap = [])
    {
        $gradeTotals = [];
        $rowTotals = [];
        $inspectingTotals = [];
        $isPartialMap = [];
        $receiptDates = [];

        if (empty($ids)) {
            return [
                'gradeTotals' => $gradeTotals,
                'rowTotals' => $rowTotals,
                'inspectingTotals' => $inspectingTotals,
                'isPartialMap' => $isPartialMap,
                'receiptDates' => $receiptDates,
            ];
        }

        $cfg = self::getSourceConfig($type);
        $itemTable = $cfg['itemTable'];
        $gjTable = TrnGudangJadi::tableName();
        $transFrom = $cfg['transFrom'];
        $ids = array_values(array_unique(array_map('intval', $ids)));

        // 1. Received grade sums from trn_gudang_jadi (respecting deliveredAtRange if set)
        $gjRowsQuery = (new Query())
            ->select([
                'ii.inspecting_id',
                'gj.grade',
                'SUM(gj.qty) AS total_qty',
                'MIN(gj.date) AS min_date',
                'MAX(gj.date) AS max_date',
            ])
            ->from(['ii' => $itemTable])
            ->innerJoin(['gj' => $gjTable], 'gj.trans_from = :tf AND gj.id_from = ii.id', [':tf' => $transFrom])
            ->where(['ii.inspecting_id' => $ids])
            ->groupBy(['ii.inspecting_id', 'gj.grade']);

        if ($fromDeliv !== null && $toDeliv !== null) {
            $gjRowsQuery->andWhere(['between', 'gj.date', $fromDeliv, $toDeliv]);
        }

        $gjRows = $gjRowsQuery->all();
        $hasGjIds = [];
        $minDates = [];
        $maxDates = [];

        foreach ($gjRows as $r) {
            $id = (int)$r['inspecting_id'];
            $key = $keyPrefix . $id;
            $grade = (int)$r['grade'];
            $qty = (float)$r['total_qty'];

            $hasGjIds[$id] = true;
            $gradeTotals[$key][$grade] = (isset($gradeTotals[$key][$grade]) ? $gradeTotals[$key][$grade] : 0) + $qty;
            $rowTotals[$key] = (isset($rowTotals[$key]) ? $rowTotals[$key] : 0) + $qty;

            if (!empty($r['min_date'])) {
                if (!isset($minDates[$id]) || $r['min_date'] < $minDates[$id]) {
                    $minDates[$id] = $r['min_date'];
                }
            }
            if (!empty($r['max_date'])) {
                if (!isset($maxDates[$id]) || $r['max_date'] > $maxDates[$id]) {
                    $maxDates[$id] = $r['max_date'];
                }
            }
        }

        // 2. Total inspecting qty per document (for partial vs full comparison)
        $inspRows = (new Query())
            ->select(['inspecting_id', 'SUM(qty) AS total_qty'])
            ->from($itemTable)
            ->where(['inspecting_id' => $ids])
            ->groupBy(['inspecting_id'])
            ->all();

        $rawInspTotals = [];
        foreach ($inspRows as $r) {
            $id = (int)$r['inspecting_id'];
            $rawInspTotals[$id] = (float)$r['total_qty'];
            $inspectingTotals[$keyPrefix . $id] = (float)$r['total_qty'];
        }

        // 3. All-time received qty per document (only needed when deliveredAtRange filters gjRows)
        $allTimeGjTotals = [];
        $allTimeHasGj = [];
        if ($fromDeliv !== null && $toDeliv !== null) {
            $allGjRows = (new Query())
                ->select(['ii.inspecting_id', 'SUM(gj.qty) AS total_qty'])
                ->from(['ii' => $itemTable])
                ->innerJoin(['gj' => $gjTable], 'gj.trans_from = :tf AND gj.id_from = ii.id', [':tf' => $transFrom])
                ->where(['ii.inspecting_id' => $ids])
                ->groupBy(['ii.inspecting_id'])
                ->all();
            foreach ($allGjRows as $r) {
                $id = (int)$r['inspecting_id'];
                $allTimeHasGj[$id] = true;
                $allTimeGjTotals[$id] = (float)$r['total_qty'];
            }
        } else {
            foreach ($ids as $id) {
                if (isset($hasGjIds[$id])) {
                    $allTimeHasGj[$id] = true;
                    $allTimeGjTotals[$id] = isset($rowTotals[$keyPrefix . $id]) ? $rowTotals[$keyPrefix . $id] : 0;
                }
            }
        }

        // 4. Legacy fallback for pre-2023 docs with zero trn_gudang_jadi link
        $legacyIds = [];
        foreach ($ids as $id) {
            if (!isset($allTimeHasGj[$id])) {
                $legacyIds[] = $id;
            }
        }
        if (!empty($legacyIds)) {
            $legacyRows = (new Query())
                ->select(['inspecting_id', 'grade', 'SUM(qty) AS total_qty'])
                ->from($itemTable)
                ->where(['inspecting_id' => $legacyIds])
                ->groupBy(['inspecting_id', 'grade'])
                ->all();
            foreach ($legacyRows as $r) {
                $id = (int)$r['inspecting_id'];
                $key = $keyPrefix . $id;
                $grade = (int)$r['grade'];
                $qty = (float)$r['total_qty'];
                $gradeTotals[$key][$grade] = (isset($gradeTotals[$key][$grade]) ? $gradeTotals[$key][$grade] : 0) + $qty;
                $rowTotals[$key] = (isset($rowTotals[$key]) ? $rowTotals[$key] : 0) + $qty;
            }
        }

        // 5. Build receiptDates and isPartialMap
        $formatter = Yii::$app->formatter;
        foreach ($ids as $id) {
            $key = $keyPrefix . $id;

            if (isset($minDates[$id]) && isset($maxDates[$id])) {
                if ($minDates[$id] === $maxDates[$id]) {
                    $receiptDates[$key] = $formatter->asDate($maxDates[$id]);
                } else {
                    $receiptDates[$key] = $formatter->asDate($minDates[$id]) . ' - ' . $formatter->asDate($maxDates[$id]);
                }
            } elseif (isset($deliveredAtMap[$id]) && !empty($deliveredAtMap[$id])) {
                $receiptDates[$key] = $formatter->asDate($deliveredAtMap[$id]);
            } else {
                $receiptDates[$key] = '-';
            }

            $status = isset($statusMap[$id]) ? (int)$statusMap[$id] : $cfg['deliveredStatus'];
            $totalInsp = isset($rawInspTotals[$id]) ? $rawInspTotals[$id] : 0;
            $totalRecvAll = isset($allTimeGjTotals[$id]) ? $allTimeGjTotals[$id] : $totalInsp;

            $isPartialMap[$key] = ($status !== $cfg['deliveredStatus'])
                || (isset($allTimeHasGj[$id]) && $totalRecvAll + 0.0001 < $totalInsp);
        }

        return [
            'gradeTotals' => $gradeTotals,
            'rowTotals' => $rowTotals,
            'inspectingTotals' => $inspectingTotals,
            'isPartialMap' => $isPartialMap,
            'receiptDates' => $receiptDates,
        ];
    }

    /**
     * Mapping from sort attribute names to grade constants.
     *
     * @return array
     */
    public static function gradeSortMap()
    {
        return [
            'grade_a' => InspectingItem::GRADE_A,
            'grade_a_plus' => InspectingItem::GRADE_A_PLUS,
            'grade_a_asterisk' => InspectingItem::GRADE_A_ASTERISK,
            'grade_b' => InspectingItem::GRADE_B,
            'grade_c' => InspectingItem::GRADE_C,
            'grade_pk' => InspectingItem::GRADE_PK,
            'grade_sample' => InspectingItem::GRADE_SAMPLE,
        ];
    }

    /**
     * Builds a correlated SQL expression returning the accurate received qty for a single source row
     * (from trn_gudang_jadi, with legacy pre-2023 fallback).
     *
     * @param string $type 'PACKING' or 'MKL_BJ'
     * @param string $headerAlias
     * @param int|null $grade Null for total across all grades
     * @param string|null $fromDeliv
     * @param string|null $toDeliv
     * @return string
     */
    protected static function buildSingleSourceReceiptSortSql($type, $headerAlias, $grade = null, $fromDeliv = null, $toDeliv = null)
    {
        $cfg = self::getSourceConfig($type);
        $itemTable = $cfg['itemTable'];
        $gjTable = TrnGudangJadi::tableName();
        $db = Yii::$app->db;
        $tfLiteral = $db->quoteValue($cfg['transFrom']);

        $gradeCondGj = $grade !== null ? ' AND gj.grade = ' . (int)$grade : '';
        $gradeCondItem = $grade !== null ? ' AND ii3.grade = ' . (int)$grade : '';
        $dateCondGj = ($fromDeliv !== null && $toDeliv !== null)
            ? ' AND gj.date BETWEEN ' . $db->quoteValue($fromDeliv) . ' AND ' . $db->quoteValue($toDeliv)
            : '';

        return "COALESCE(
            (SELECT SUM(gj.qty)
             FROM {$itemTable} ii
             INNER JOIN {$gjTable} gj ON gj.trans_from = {$tfLiteral} AND gj.id_from = ii.id
             WHERE ii.inspecting_id = {$headerAlias}.id{$gradeCondGj}{$dateCondGj}),
            CASE WHEN NOT EXISTS (
                SELECT 1
                FROM {$itemTable} ii2
                INNER JOIN {$gjTable} gj2 ON gj2.trans_from = {$tfLiteral} AND gj2.id_from = ii2.id
                WHERE ii2.inspecting_id = {$headerAlias}.id
            ) THEN (
                SELECT COALESCE(SUM(ii3.qty), 0)
                FROM {$itemTable} ii3
                WHERE ii3.inspecting_id = {$headerAlias}.id{$gradeCondItem}
            ) ELSE 0 END
        )";
    }

    /**
     * Registers sortable attributes for all grade columns, Total Diterima, and Total Inspecting.
     *
     * @param \yii\data\Sort $sort
     * @param string $mode 'PACKING', 'MKL_BJ', or 'COMBINED'
     * @param string $headerAlias
     * @param string|null $fromDeliv
     * @param string|null $toDeliv
     * @return void
     */
    public static function applyGradeSortAttributes($sort, $mode, $headerAlias, $fromDeliv = null, $toDeliv = null)
    {
        $idTieBreaker = "{$headerAlias}.id";

        $makeSortDef = function ($sqlExpr) use ($idTieBreaker) {
            return [
                'asc' => [new Expression("({$sqlExpr}) ASC"), $idTieBreaker => SORT_DESC],
                'desc' => [new Expression("({$sqlExpr}) DESC"), $idTieBreaker => SORT_DESC],
                'default' => SORT_DESC,
            ];
        };

        $buildExpr = function ($grade) use ($mode, $headerAlias, $fromDeliv, $toDeliv) {
            if ($mode === 'COMBINED') {
                $pSql = self::buildSingleSourceReceiptSortSql('PACKING', $headerAlias, $grade, $fromDeliv, $toDeliv);
                $mSql = self::buildSingleSourceReceiptSortSql('MKL_BJ', $headerAlias, $grade, $fromDeliv, $toDeliv);
                return "CASE WHEN {$headerAlias}.source = 'PACKING' THEN ({$pSql}) ELSE ({$mSql}) END";
            }
            return self::buildSingleSourceReceiptSortSql($mode, $headerAlias, $grade, $fromDeliv, $toDeliv);
        };

        foreach (self::gradeSortMap() as $attr => $gradeConst) {
            $sort->attributes[$attr] = $makeSortDef($buildExpr($gradeConst));
        }

        $sort->attributes['total_diterima'] = $makeSortDef($buildExpr(null));

        if ($mode === 'COMBINED') {
            $pTable = InspectingItem::tableName();
            $mTable = InspectingMklBjItems::tableName();
            $inspSql = "CASE WHEN {$headerAlias}.source = 'PACKING'
                THEN COALESCE((SELECT SUM(ii.qty) FROM {$pTable} ii WHERE ii.inspecting_id = {$headerAlias}.id), 0)
                ELSE COALESCE((SELECT SUM(mi.qty) FROM {$mTable} mi WHERE mi.inspecting_id = {$headerAlias}.id), 0)
                END";
        } else {
            $cfg = self::getSourceConfig($mode);
            $itemTable = $cfg['itemTable'];
            $inspSql = "COALESCE((SELECT SUM(ii.qty) FROM {$itemTable} ii WHERE ii.inspecting_id = {$headerAlias}.id), 0)";
        }
        $sort->attributes['total_inspecting'] = $makeSortDef($inspSql);

        // Force recalculation in case Sort::getOrders() was already invoked during search model filtering.
        $sort->getOrders(true);
    }

    /**
     * Registers CSS and interactive 3D perspective JS for Gudang Jadi receiving views.
     *
     * @param \yii\web\View $view
     * @return void
     */
    public static function registerResponsiveGridAssets($view)
    {
        $css = <<<'CSS'
/* 3D Elevated Summary Panel & KPI Cards */
.gj-summary-box {
    border: 1px solid #dbe4f0;
    border-top: 3px solid #2563eb;
    border-radius: 12px;
    background: linear-gradient(160deg, #ffffff 0%, #f4f7fb 100%);
    box-shadow: 0 14px 30px -12px rgba(15, 23, 42, 0.22), 0 4px 10px -2px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    overflow: hidden;
    margin-bottom: 16px;
}
.gj-summary-box .box-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #e2e8f0;
}
.gj-summary-box .box-header .box-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    text-shadow: 0 1px 0 #ffffff;
}
.gj-summary-box .box-header .pull-right {
    float: none !important;
    margin-left: auto;
}
.gj-summary-meta {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}
.gj-meta-pill {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
    color: #1e40af;
    border: 1px solid #bfdbfe;
    box-shadow: 0 2px 0 #bfdbfe;
    font-weight: 600;
}
.gj-3d-units-wrap {
    padding: 12px 14px 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.gj-3d-unit-group {
    background: rgba(248, 250, 252, 0.9);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px;
    box-shadow: inset 0 1px 0 #ffffff, 0 3px 8px -4px rgba(15, 23, 42, 0.08);
}
.gj-3d-cards-grid {
    display: grid;
    grid-template-columns: 1.25fr repeat(7, minmax(0, 1fr));
    gap: 10px;
}
@media (max-width: 1360px) {
    .gj-3d-cards-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
@media (max-width: 767px) {
    .gj-3d-cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
.gj-3d-card {
    --rx: 0deg;
    --ry: 0deg;
    --mx: 50%;
    --my: 50%;
    position: relative;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 76px;
    padding: 11px 14px;
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
    border: 1px solid #dbe4f0;
    box-shadow: 0 4px 0 #cbd5e1, 0 8px 16px -6px rgba(15, 23, 42, 0.14), inset 0 1px 0 rgba(255, 255, 255, 0.95);
    transform: perspective(800px) rotateX(var(--rx)) rotateY(var(--ry));
    transition: transform 0.18s ease-out, box-shadow 0.2s ease-out, border-color 0.2s ease-out;
    will-change: transform;
}
.gj-3d-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 10px;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(255, 255, 255, 0.75) 0%, rgba(255, 255, 255, 0) 65%);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.gj-3d-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 6px 0 #93c5fd, 0 14px 22px -8px rgba(37, 99, 235, 0.24), inset 0 1px 0 #ffffff;
}
.gj-3d-card:hover::before {
    opacity: 1;
}
.gj-3d-card:active {
    transform: perspective(800px) rotateX(0deg) rotateY(0deg) translateY(2px);
    box-shadow: 0 2px 0 #93c5fd, 0 5px 10px -4px rgba(37, 99, 235, 0.22);
}
.gj-3d-card-hero {
    background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 100%);
    border-color: #1e40af;
    color: #ffffff;
    box-shadow: 0 5px 0 #1e3a8a, 0 12px 20px -6px rgba(29, 78, 216, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.32);
}
.gj-3d-card-hero::before {
    background: radial-gradient(circle at var(--mx) var(--my), rgba(255, 255, 255, 0.28) 0%, rgba(255, 255, 255, 0) 65%);
}
.gj-3d-card-hero:hover {
    border-color: #60a5fa;
    box-shadow: 0 7px 0 #1e3a8a, 0 16px 24px -6px rgba(29, 78, 216, 0.48), inset 0 1px 0 rgba(255, 255, 255, 0.42);
}
.gj-3d-card-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1.25;
    color: #64748b;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.gj-3d-card-hero .gj-3d-card-label {
    color: #dbeafe;
}
.gj-3d-card-value {
    font-size: 15px;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    color: #0f172a;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.gj-3d-card-hero .gj-3d-card-value {
    color: #ffffff;
    font-size: 16px;
    text-shadow: 0 1px 3px rgba(15, 23, 42, 0.28);
}
.gj-3d-card-unit {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-top: 3px;
}
.gj-3d-card-hero .gj-3d-card-unit {
    color: #bfdbfe;
}
.gj-3d-card.tone-a { border-top: 3px solid #10b981; }
.gj-3d-card.tone-b { border-top: 3px solid #3b82f6; }
.gj-3d-card.tone-c { border-top: 3px solid #f59e0b; }
.gj-3d-card.tone-pk { border-top: 3px solid #8b5cf6; }

/* 3D Elevated Grid Panel & Tactile Buttons */
.gj-responsive-grid .panel {
    margin-bottom: 18px;
    border-radius: 12px;
    border: 1px solid #dbe4f0;
    box-shadow: 0 16px 34px -14px rgba(15, 23, 42, 0.22), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    background: #ffffff;
}
.gj-responsive-grid .panel > .panel-heading {
    background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
    border-bottom: 1px solid #dbe4f0;
}
.gj-responsive-grid .kv-panel-before .btn,
.gj-responsive-grid .kv-grid-toolbar .btn {
    border-radius: 7px !important;
    transition: transform 0.14s ease, box-shadow 0.14s ease;
    box-shadow: 0 3px 0 #cbd5e1, 0 4px 8px rgba(15, 23, 42, 0.08);
    margin-bottom: 3px;
}
.gj-responsive-grid .kv-panel-before .btn-primary {
    box-shadow: 0 3px 0 #1e40af, 0 5px 12px rgba(37, 99, 235, 0.3);
}
.gj-responsive-grid .kv-panel-before .btn:hover,
.gj-responsive-grid .kv-grid-toolbar .btn:hover {
    transform: translateY(-2px);
}
.gj-responsive-grid .kv-panel-before .btn:active,
.gj-responsive-grid .kv-grid-toolbar .btn:active {
    transform: translateY(1px);
    box-shadow: 0 1px 0 #cbd5e1;
}
.gj-responsive-grid .gj-per-page-group {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 4px;
    vertical-align: middle;
}
.gj-responsive-grid .gj-per-page-group .btn {
    margin-left: 0 !important;
}

/* Scrollable Grid, Sticky Header + Search Row, Depth Hover */
.gj-responsive-grid .gj-grid-scroll {
    width: 100%;
    overflow-x: auto !important;
    overflow-y: auto !important;
    max-height: 75vh;
    -webkit-overflow-scrolling: touch;
}
.gj-responsive-grid .gj-grid-table {
    width: auto !important;
    min-width: 100%;
    margin-bottom: 0;
    table-layout: auto !important;
}
.gj-responsive-grid .gj-grid-table > thead > tr > th,
.gj-responsive-grid .gj-grid-table > thead > tr > td,
.gj-responsive-grid .gj-grid-table > tbody > tr > td,
.gj-responsive-grid .gj-grid-table > tfoot > tr > td {
    white-space: nowrap;
    vertical-align: middle;
}
.gj-responsive-grid .gj-grid-table > thead {
    position: sticky;
    top: 0;
    z-index: 4;
    box-shadow: 0 6px 14px -6px rgba(15, 23, 42, 0.18);
}
.gj-responsive-grid .gj-grid-table > thead > tr:first-child > th {
    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    background-clip: padding-box;
    box-shadow: inset 0 -1px 0 #cbd5e1;
    color: #0f172a;
}
.gj-responsive-grid .gj-grid-table > thead > tr.filters > td {
    background-color: #ffffff;
    background-clip: padding-box;
    box-shadow: inset 0 -2px 0 #cbd5e1;
}
.gj-responsive-grid .gj-grid-table > tbody > tr {
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}
.gj-responsive-grid .gj-grid-table > tbody > tr:hover > td {
    background-color: #eff6ff !important;
}
.gj-responsive-grid .gj-grid-table > tbody > tr:hover > td:first-child {
    box-shadow: inset 4px 0 0 #2563eb;
}
.gj-responsive-grid .gj-grid-table .label {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    box-shadow: 0 2px 0 rgba(15, 23, 42, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.35);
}
.gj-responsive-grid .gj-grid-table tr.filters .form-control {
    min-width: 115px;
    border-radius: 6px;
}
.gj-responsive-grid .gj-grid-table tr.filters .kv-drp-container,
.gj-responsive-grid .gj-grid-table tr.filters input[name*="Range"] {
    min-width: 195px;
}
.gj-responsive-grid .gj-grid-table tr.filters .select2-container {
    min-width: 120px;
}
@media (prefers-reduced-motion: reduce) {
    .gj-3d-card,
    .gj-responsive-grid .kv-panel-before .btn,
    .gj-responsive-grid .kv-grid-toolbar .btn {
        transition: none !important;
        transform: none !important;
    }
}
CSS;
        $view->registerCss($css, [], 'gj-responsive-grid-css');

        $js = <<<'JS'
(function () {
    if (window.matchMedia && (!window.matchMedia('(hover: hover)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
        return;
    }
    var activeCard = null;
    document.addEventListener('mousemove', function (e) {
        var card = e.target.closest ? e.target.closest('.gj-3d-card') : null;
        if (activeCard && activeCard !== card) {
            activeCard.style.setProperty('--rx', '0deg');
            activeCard.style.setProperty('--ry', '0deg');
        }
        activeCard = card;
        if (!card) {
            return;
        }
        var rect = card.getBoundingClientRect();
        if (!rect.width || !rect.height) {
            return;
        }
        var x = (e.clientX - rect.left) / rect.width;
        var y = (e.clientY - rect.top) / rect.height;
        var ry = (x - 0.5) * 11;
        var rx = (0.5 - y) * 11;
        card.style.setProperty('--rx', rx.toFixed(2) + 'deg');
        card.style.setProperty('--ry', ry.toFixed(2) + 'deg');
        card.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
        card.style.setProperty('--my', (y * 100).toFixed(1) + '%');
    }, { passive: true });
    document.addEventListener('mouseleave', function () {
        if (activeCard) {
            activeCard.style.setProperty('--rx', '0deg');
            activeCard.style.setProperty('--ry', '0deg');
            activeCard = null;
        }
    });
})();
JS;
        $view->registerJs($js, \yii\web\View::POS_READY, 'gj-3d-tilt-js');
    }

    /**
     * Renders the 3D interactive summary box grouped by unit and grade.
     *
     * @param array $config keys: title, periodeParts, totalCount, summaryGrades, summaryByUnit, formatter
     * @return string
     */
    public static function renderSummaryBox(array $config)
    {
        $title = isset($config['title']) ? $config['title'] : 'Total Hasil Pencarian';
        $periodeParts = isset($config['periodeParts']) ? $config['periodeParts'] : [];
        $totalCount = isset($config['totalCount']) ? (int)$config['totalCount'] : 0;
        $summaryGrades = isset($config['summaryGrades']) ? $config['summaryGrades'] : [];
        $summaryByUnit = isset($config['summaryByUnit']) ? $config['summaryByUnit'] : [];
        $formatter = isset($config['formatter']) ? $config['formatter'] : Yii::$app->formatter;

        $toneClasses = [
            InspectingItem::GRADE_A => 'tone-a',
            InspectingItem::GRADE_A_PLUS => 'tone-a',
            InspectingItem::GRADE_A_ASTERISK => 'tone-a',
            InspectingItem::GRADE_B => 'tone-b',
            InspectingItem::GRADE_C => 'tone-c',
            InspectingItem::GRADE_PK => 'tone-pk',
            InspectingItem::GRADE_SAMPLE => 'tone-pk',
        ];

        $out = '<div class="box box-primary gj-summary-box">';
        $out .= '<div class="box-header with-border">';
        $out .= '<h3 class="box-title"><i class="fa fa-cubes text-primary"></i> ' . Html::encode($title) . '</h3>';
        $out .= '<div class="gj-summary-meta pull-right">';
        if (!empty($periodeParts)) {
            $out .= '<span class="gj-meta-pill"><i class="fa fa-calendar"></i> ' . Html::encode(implode(' | ', $periodeParts)) . '</span>';
        }
        $out .= '<span class="gj-meta-pill">' . $formatter->asInteger($totalCount) . ' data</span>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="box-body no-padding">';
        if (empty($summaryByUnit)) {
            $out .= '<div style="padding: 14px 16px;" class="text-muted">Tidak ada total grade untuk data yang dicari.</div>';
        } else {
            $out .= '<div class="gj-3d-units-wrap">';
            foreach ($summaryByUnit as $unitSummary) {
                $unitLabel = $unitSummary['unitLabel'];
                $out .= '<div class="gj-3d-unit-group">';
                $out .= '<div class="gj-3d-cards-grid">';

                $out .= '<div class="gj-3d-card gj-3d-card-hero">';
                $out .= '<div class="gj-3d-card-label">Total Grade</div>';
                $out .= '<div class="gj-3d-card-value">' . $formatter->asDecimal($unitSummary['total']) . '</div>';
                $out .= '<div class="gj-3d-card-unit">' . Html::encode($unitLabel) . '</div>';
                $out .= '</div>';

                foreach ($summaryGrades as $gradeKey => $gradeLabel) {
                    $val = isset($unitSummary['grades'][$gradeKey]) ? $unitSummary['grades'][$gradeKey] : 0;
                    $tone = isset($toneClasses[$gradeKey]) ? $toneClasses[$gradeKey] : 'tone-b';
                    $out .= '<div class="gj-3d-card ' . $tone . '">';
                    $out .= '<div class="gj-3d-card-label">' . Html::encode($gradeLabel) . '</div>';
                    $out .= '<div class="gj-3d-card-value">' . $formatter->asDecimal($val) . '</div>';
                    $out .= '<div class="gj-3d-card-unit">' . Html::encode($unitLabel) . '</div>';
                    $out .= '</div>';
                }

                $out .= '</div>';
                $out .= '</div>';
            }
            $out .= '</div>';
        }
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
}
