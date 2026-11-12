<?php
namespace backend\controllers;

use backend\components\PenerimaanGudangJadiHelper;
use backend\models\search\PenerimaanGabunganSearch;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GudangJadiController extends Controller
{
    /**
     * Lists received Packing models (including partial receipts recorded in trn_gudang_jadi).
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnInspectingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->defaultPageSize = 50;
        $dataProvider->pagination->pageSizeLimit = [10, 200];

        $validation = PenerimaanGudangJadiHelper::validateEitherDateRange($searchModel->dateRange, $searchModel->deliveredAtRange);
        $hasValidDateRange = $validation['hasValidDateRange'];
        if ($validation['error'] !== null) {
            Yii::$app->session->setFlash('error', $validation['error']);
        }

        $fromDeliv = $validation['penerimaan']['from'];
        $toDeliv = $validation['penerimaan']['to'];

        $pageDetails = [
            'gradeTotals' => [],
            'rowTotals' => [],
            'inspectingTotals' => [],
            'isPartialMap' => [],
            'receiptDates' => [],
        ];
        $summaryByUnit = [];

        if (!$hasValidDateRange) {
            $dataProvider->query->where('0=1');
        } else {
            PenerimaanGudangJadiHelper::applyReceiptFilter($dataProvider->query, 'PACKING', 'trn_inspecting', $fromDeliv, $toDeliv);
            PenerimaanGudangJadiHelper::applyGradeSortAttributes($dataProvider->sort, 'PACKING', 'trn_inspecting', $fromDeliv, $toDeliv);

            $docSubQuery = (clone $dataProvider->query)
                ->select([
                    'trn_inspecting.id',
                    'trn_inspecting.unit',
                    'trn_inspecting.status AS doc_status',
                    'trn_inspecting.delivered_at',
                ])
                ->orderBy(null)
                ->limit(null)
                ->offset(null);

            PenerimaanGudangJadiHelper::accumulateSummary($docSubQuery, 'PACKING', $fromDeliv, $toDeliv, $summaryByUnit);
            ksort($summaryByUnit);

            $dataProvider->query->with([
                'kartuProcessDyeing.scGreige',
                'kartuProcessPrinting.scGreige',
                'memoRepair.scGreige',
            ]);

            $models = $dataProvider->getModels();
            $ids = [];
            $statusMap = [];
            $deliveredAtMap = [];
            foreach ($models as $m) {
                $ids[] = $m->id;
                $statusMap[$m->id] = $m->status;
                $deliveredAtMap[$m->id] = $m->delivered_at;
            }

            $pageDetails = PenerimaanGudangJadiHelper::computePageDetails($ids, 'PACKING', '', $fromDeliv, $toDeliv, $statusMap, $deliveredAtMap);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gradeTotals' => $pageDetails['gradeTotals'],
            'rowTotals' => $pageDetails['rowTotals'],
            'inspectingTotals' => $pageDetails['inspectingTotals'],
            'isPartialMap' => $pageDetails['isPartialMap'],
            'receiptDates' => $pageDetails['receiptDates'],
            'hasValidDateRange' => $hasValidDateRange,
            'summaryByUnit' => $summaryByUnit,
        ]);
    }

    /**
     * Displays a single Inspecting model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (($model = TrnInspecting::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Combined history page for Packing (trn_inspecting) and Packing MKL BJ (inspecting_mkl_bj),
     * including partial receipts recorded in trn_gudang_jadi.
     * @return mixed
     */
    public function actionRiwayatGabungan()
    {
        $searchModel = new PenerimaanGabunganSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($searchModel->dateRangeError !== null) {
            Yii::$app->session->setFlash('error', $searchModel->dateRangeError);
        }

        $fromDeliv = $searchModel->from_delivered_at;
        $toDeliv = $searchModel->to_delivered_at;

        $gradeTotals = [];
        $rowTotals = [];
        $inspectingTotals = [];
        $isPartialMap = [];
        $receiptDates = [];
        $summaryByUnit = [];

        if ($searchModel->hasValidDateRange) {
            $packingSub = $searchModel->buildSummarySourceQuery(PenerimaanGabunganSearch::SOURCE_PACKING);
            if ($packingSub !== null) {
                PenerimaanGudangJadiHelper::accumulateSummary($packingSub, 'PACKING', $fromDeliv, $toDeliv, $summaryByUnit);
            }

            $mklSub = $searchModel->buildSummarySourceQuery(PenerimaanGabunganSearch::SOURCE_MKL_BJ);
            if ($mklSub !== null) {
                PenerimaanGudangJadiHelper::accumulateSummary($mklSub, 'MKL_BJ', $fromDeliv, $toDeliv, $summaryByUnit);
            }

            ksort($summaryByUnit);

            $models = $dataProvider->getModels();
            $packingIds = [];
            $packingStatus = [];
            $packingDeliv = [];
            $mklIds = [];
            $mklStatus = [];
            $mklDeliv = [];

            foreach ($models as $row) {
                $id = (int)$row['id'];
                if ($row['source'] === PenerimaanGabunganSearch::SOURCE_PACKING) {
                    $packingIds[] = $id;
                    $packingStatus[$id] = (int)$row['doc_status'];
                    $packingDeliv[$id] = $row['delivered_at'];
                } else {
                    $mklIds[] = $id;
                    $mklStatus[$id] = (int)$row['doc_status'];
                    $mklDeliv[$id] = $row['delivered_at'];
                }
            }

            $packingDetails = PenerimaanGudangJadiHelper::computePageDetails(
                $packingIds,
                'PACKING',
                PenerimaanGabunganSearch::SOURCE_PACKING . '_',
                $fromDeliv,
                $toDeliv,
                $packingStatus,
                $packingDeliv
            );

            $mklDetails = PenerimaanGudangJadiHelper::computePageDetails(
                $mklIds,
                'MKL_BJ',
                PenerimaanGabunganSearch::SOURCE_MKL_BJ . '_',
                $fromDeliv,
                $toDeliv,
                $mklStatus,
                $mklDeliv
            );

            $gradeTotals = $packingDetails['gradeTotals'] + $mklDetails['gradeTotals'];
            $rowTotals = $packingDetails['rowTotals'] + $mklDetails['rowTotals'];
            $inspectingTotals = $packingDetails['inspectingTotals'] + $mklDetails['inspectingTotals'];
            $isPartialMap = $packingDetails['isPartialMap'] + $mklDetails['isPartialMap'];
            $receiptDates = $packingDetails['receiptDates'] + $mklDetails['receiptDates'];
        }

        return $this->render('riwayat-gabungan', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gradeTotals' => $gradeTotals,
            'rowTotals' => $rowTotals,
            'inspectingTotals' => $inspectingTotals,
            'isPartialMap' => $isPartialMap,
            'receiptDates' => $receiptDates,
            'hasValidDateRange' => $searchModel->hasValidDateRange,
            'summaryByUnit' => $summaryByUnit,
        ]);
    }
}
