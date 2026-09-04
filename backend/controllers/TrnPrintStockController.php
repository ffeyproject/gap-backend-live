<?php

namespace backend\controllers;

use common\models\ar\{ TrnInspecting, InspectingMklBj, TrnGudangJadi, TrnGudangJadiSearch, TrnGudangJadiOpnamePcs };
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\{ Query, Expression };
use yii\data\ArrayDataProvider;

/**
 * TrnPrintStockController implements the R actions for TrnGudangJadi model.
 */
class TrnPrintStockController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnGudangJadi models / Stok Opname Pcs for printing palet sheets.
     * @return mixed
     */
    public function actionIndex()
    {
        $timestamp = date('l, d F Y H:i:s');
        $searchModel = Yii::$app->request->getQueryParams();
        $subLocParam = Yii::$app->request->get('sub_location');
        if (empty($subLocParam) && !empty($searchModel['sub_location'])) {
            $subLocParam = $searchModel['sub_location'];
        }
        $subLocParam = trim((string)$subLocParam);

        $sumberDataParam = Yii::$app->request->get('sumber_data', 'auto');
        if (!in_array($sumberDataParam, ['auto', 'opname', 'system'])) {
            $sumberDataParam = 'auto';
        }

        $results = [];
        $sumberDataUsed = 'system';

        if (!empty($subLocParam)) {
            $opnameCount = TrnGudangJadiOpnamePcs::find()->where(['locs_code' => $subLocParam])->count();

            // Jika mode opname dipilih atau (mode auto dan lokasi ini memiliki data opname)
            if ($sumberDataParam === 'opname' || ($sumberDataParam === 'auto' && $opnameCount > 0)) {
                $sumberDataUsed = 'opname';
                $opnameRows = TrnGudangJadiOpnamePcs::find()
                    ->alias('t')
                    ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
                    ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
                    ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
                    ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
                    ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
                    ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id')
                    ->where(['t.locs_code' => $subLocParam])
                    ->orderBy(['t.id' => SORT_ASC])
                    ->all();

                $final_result = [];
                $no_wo_map = [];

                foreach ($opnameRows as $opRow) {
                    $no_wo = ($opRow->gudangJadi && $opRow->gudangJadi->wo) ? $opRow->gudangJadi->wo->no : '-';
                    $design = ($opRow->gudangJadi && $opRow->gudangJadi->wo) ? $opRow->gudangJadi->wo->greigeNamaKain : (!empty($opRow->qr_code_desc) ? $opRow->qr_code_desc : '-');
                    $color = ($opRow->gudangJadi && !empty($opRow->gudangJadi->color)) ? $opRow->gudangJadi->color : '-';
                    $unit = (int)$opRow->unit;
                    $qty = (float)$opRow->qty;
                    $grade = (int)$opRow->grade;
                    $itemObj = [
                        'id' => $opRow->id,
                        'qty' => $qty,
                        'grade' => $grade,
                        'unit' => $unit,
                        'note' => $opRow->remark ?: ($opRow->gudangJadi ? $opRow->gudangJadi->note : ''),
                        'hasil_pemotongan' => $opRow->gudangJadi ? $opRow->gudangJadi->hasil_pemotongan : false,
                        'opname_code' => $opRow->opname_code,
                    ];

                    if (isset($no_wo_map[$no_wo])) {
                        if (isset($final_result[$no_wo_map[$no_wo]]['colors'][$color])) {
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color]['qty'][] = $itemObj;
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color]['total_qty'] += $qty;
                        } else {
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color] = [
                                'qty' => [$itemObj],
                                'total_qty' => $qty,
                            ];
                        }
                    } else {
                        $final_result[] = [
                            'unit' => $unit,
                            'no_wo' => $no_wo,
                            'design' => $design,
                            'colors' => [
                                $color => [
                                    'qty' => [$itemObj],
                                    'total_qty' => $qty,
                                ]
                            ]
                        ];
                        $no_wo_map[$no_wo] = count($final_result) - 1;
                    }
                }
                $results = $final_result;
            } else {
                $sumberDataUsed = 'system';
                $query = (new Query())
                    ->select([
                        'trn_wo.no no_wo',
                        'mst_greige.nama_kain mst_greige_nama_kain',
                        'mst_greige_group.nama_kain mst_greige_group_nama_kain',
                        'trn_sc_greige.process sc_greige_jenis_proses',
                        'trn_mo.process trn_mo_jenis_proses',
                        'trn_mo.article trn_mo_article',
                        'trn_mo.design trn_mo_design',
                        'trn_sc_greige.lebar_kain sc_greige_lebar_kain',
                        'trn_gudang_jadi.id',
                        'trn_gudang_jadi.note',
                        'trn_gudang_jadi.hasil_pemotongan',
                        'trn_gudang_jadi.unit',
                        'trn_gudang_jadi.grade',
                        'trn_gudang_jadi.source',
                        'trn_gudang_jadi.source_ref',
                        'trn_gudang_jadi.status',
                        'trn_gudang_jadi.color',
                        'trn_gudang_jadi.jenis_gudang',
                        'trn_gudang_jadi.qty'
                    ])
                    ->leftJoin('trn_wo', 'trn_gudang_jadi.wo_id = trn_wo.id')
                    ->leftJoin('trn_mo', 'trn_wo.mo_id = trn_mo.id')
                    ->leftJoin('trn_sc_greige', 'trn_wo.sc_greige_id = trn_sc_greige.id')
                    ->leftJoin('mst_greige', 'trn_wo.greige_id = mst_greige.id')
                    ->leftJoin('mst_greige_group', 'trn_sc_greige.greige_group_id = mst_greige_group.id')
                    ->where(['=', 'trn_gudang_jadi.status', TrnGudangJadi::STATUS_STOCK])
                    ->andWhere(['=', 'trn_gudang_jadi.locs_code', $subLocParam])
                    ->from('trn_gudang_jadi');

                $rows = $query->all();

                $final_result = [];
                $no_wo_map = [];
                foreach ($rows as $row) {
                    if ($row['source'] == 1) { // 1 == SOURCE_PACKING
                        $getTableData = (new Query())
                            ->from(TrnInspecting::tableName())
                            ->select('*')
                            ->where(['no' => $row['source_ref']])
                            ->one();

                        if (!$getTableData) {
                            $getTableData = (new Query())
                                ->from(InspectingMklBj::tableName())
                                ->select('*')
                                ->where(['no' => $row['source_ref']])
                                ->one();
                        }

                        $processType = $getTableData['jenis_process'] ?? ($getTableData['jenis'] ?? null);
                    } else {
                        $processType = $row['trn_mo_jenis_proses'];
                    }

                    if ($processType == 1) { // 1 == dyeing
                        $is_design_or_atikel = $row['trn_mo_article'];
                    } else { // 2 == printing && // 3 == pfp
                        $articleIsNotNull = !empty($row['trn_mo_article']) ? '/' : '';
                        $is_design_or_atikel = $row['trn_mo_article'] . $articleIsNotNull . $row['trn_mo_design'];
                    }

                    $no_wo = $row['no_wo'] ?: '-';
                    $unit = (int)$row['unit'];
                    $qty = (float)$row['qty'];
                    $color = $row['color'] ?: '-';
                    $grade = (int)$row['grade'];
                    $itemObj = [
                        'id' => $row['id'],
                        'qty' => $qty,
                        'grade' => $grade,
                        'unit' => $unit,
                        'note' => $row['note'],
                        'hasil_pemotongan' => $row['hasil_pemotongan'],
                    ];

                    if (isset($no_wo_map[$no_wo])) {
                        if (isset($final_result[$no_wo_map[$no_wo]]['colors'][$color])) {
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color]['qty'][] = $itemObj;
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color]['total_qty'] += $qty;
                        } else {
                            $final_result[$no_wo_map[$no_wo]]['colors'][$color] = [
                                'qty' => [$itemObj],
                                'total_qty' => $qty,
                            ];
                        }
                    } else {
                        $final_result[] = [
                            'unit' => $unit,
                            'no_wo' => $no_wo,
                            'design' => $is_design_or_atikel ?: '-',
                            'colors' => [
                                $color => [
                                    'qty' => [$itemObj],
                                    'total_qty' => $qty,
                                ]
                            ]
                        ];
                        $no_wo_map[$no_wo] = count($final_result) - 1;
                    }
                }
                $results = $final_result;
            }
        }

        $outNotes = [];
        if (!empty($subLocParam)) {
            $outGudangJadi = (new Query())
                ->select(['note', 'status'])
                ->from('trn_gudang_jadi')
                ->where(['locs_code' => $subLocParam])
                ->andWhere(['!=', 'note', ''])
                ->andWhere(['is not', 'note', null])
                ->all();
            foreach ($outGudangJadi as $oG) {
                $outNotes[] = $oG['note'];
            }

            // Ambil catatan dari Stok Opname jika ada
            $outOpnamePcs = (new Query())
                ->select(['remark'])
                ->from('trn_gudang_jadi_opname_pcs')
                ->where(['locs_code' => $subLocParam])
                ->andWhere(['!=', 'remark', ''])
                ->andWhere(['is not', 'remark', null])
                ->all();
            foreach ($outOpnamePcs as $oP) {
                $outNotes[] = $oP['remark'];
            }

            // Ambil data pengiriman buyer (Surat Jalan / trn_kirim_buyer_header) yang berasal dari lokasi palet ini
            $kirimBuyerItems = (new Query())
                ->select([
                    'header.no as no_sj',
                    'header.date as tgl_sj',
                    'header.nama_buyer',
                    'gudang.qty',
                    'gudang.unit'
                ])
                ->from('trn_kirim_buyer_item item')
                ->innerJoin('trn_kirim_buyer kb', 'item.kirim_buyer_id = kb.id')
                ->innerJoin('trn_kirim_buyer_header header', 'kb.header_id = header.id')
                ->innerJoin('trn_gudang_jadi gudang', 'item.stock_id = gudang.id')
                ->where(['gudang.locs_code' => $subLocParam])
                ->all();

            foreach ($kirimBuyerItems as $kbItem) {
                $qtyYard = (float)$kbItem['qty'];
                $buyerName = !empty($kbItem['nama_buyer']) ? ' BUYER: ' . $kbItem['nama_buyer'] : '';
                $tglFormatted = !empty($kbItem['tgl_sj']) ? date('d/m/Y', strtotime($kbItem['tgl_sj'])) : date('d/m/Y');
                $outNotes[] = 'PENGIRIMAN BUYER ' . $qtyYard . ' YARD: NO SJ ' . $kbItem['no_sj'] . $buyerName . ' (TGL ' . $tglFormatted . ')';
            }
        }
        $outNotes = array_values(array_unique($outNotes));

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => false, // Nonaktifkan paginasi agar seluruh pcs pada palet tampil utuh saat dicetak
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'title' => !empty($subLocParam) ? $subLocParam : '-',
            'timestamp' => $timestamp,
            'outNotes' => $outNotes,
            'sumberData' => $sumberDataParam,
            'sumberDataUsed' => $sumberDataUsed,
        ]);
    }

}

