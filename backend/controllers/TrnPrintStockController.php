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
            $opnameCount = TrnGudangJadiOpnamePcs::find()
                ->where(['locs_code' => $subLocParam])
                ->andWhere(['!=', 'status', TrnGudangJadiOpnamePcs::STATUS_OUT])
                ->count();

            // Jika mode opname dipilih atau (mode auto dan lokasi ini memiliki data opname aktif)
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
                    ->andWhere(['!=', 't.status', TrnGudangJadiOpnamePcs::STATUS_OUT])
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

        $historyEntries = [];
        $recordedPotongIds = [];

        if (!empty($subLocParam)) {
            // 1. Ambil riwayat pemotongan stock langsung dari tabel trn_potong_stock
            $potongStockRows = (new Query())
                ->select([
                    'ps.id',
                    'ps.no',
                    'ps.date',
                    'ps.diperintahkan_oleh',
                    'ps.note',
                    'gj.qty as original_qty',
                    'gj.unit as original_unit',
                    'wo.no as no_wo'
                ])
                ->from('trn_potong_stock ps')
                ->innerJoin('trn_gudang_jadi gj', 'ps.stock_id = gj.id')
                ->leftJoin('trn_wo wo', 'gj.wo_id = wo.id')
                ->where(['gj.locs_code' => $subLocParam])
                ->andWhere(['ps.status' => \common\models\ar\TrnPotongStock::STATUS_POSTED])
                ->all();

            foreach ($potongStockRows as $psRow) {
                $recordedPotongIds[$psRow['id']] = true;
                $pItems = (new Query())
                    ->select(['qty'])
                    ->from('trn_potong_stock_item')
                    ->where(['potong_stock_id' => $psRow['id']])
                    ->column();

                $potongQtys = array_map('floatval', $pItems);
                $sumP = array_sum($potongQtys);
                $currentSisa = (float)$psRow['original_qty'];
                $origQty = $currentSisa + $sumP;

                $tglRaw = $psRow['date'] ?: date('Y-m-d');
                $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                $noDoc = !empty($psRow['no']) ? 'No: ' . $psRow['no'] : 'ID: ' . $psRow['id'];
                $noteText = !empty($psRow['note']) ? ' [' . trim($psRow['note']) . ']' : '';

                if ($currentSisa > 0) {
                    $descText = 'Qty Asal ' . $origQty . ' dipotong ' . implode(' + ', $potongQtys) . ' (Sisa Stock: ' . $currentSisa . ')';
                } else {
                    $descText = 'Qty Asal ' . $origQty . ' dipotong habis menjadi ' . implode(' + ', $potongQtys);
                }

                $historyEntries[] = [
                    'date' => $tglRaw,
                    'text' => $tglFormatted . ' - PEMOTONGAN STOCK (' . $noDoc . '): ' . $descText . $noteText
                ];
            }

            // 2. Ambil catatan barang keluar dari trn_gudang_jadi
            $outGudangJadi = (new Query())
                ->select(['id', 'note', 'status', 'hasil_pemotongan', 'dipotong', 'qty', 'date', 'updated_at'])
                ->from('trn_gudang_jadi')
                ->where(['locs_code' => $subLocParam])
                ->andWhere(['!=', 'note', ''])
                ->andWhere(['is not', 'note', null])
                ->all();

            foreach ($outGudangJadi as $oG) {
                $rawNote = $oG['note'];
                $parts = explode('|', $rawNote);
                foreach ($parts as $pNote) {
                    $pNote = trim($pNote);
                    if (empty($pNote)) continue;

                    // Filter teks bawaan penerimaan / sumber stok awal yang bukan mutasi
                    if (
                        strpos($pNote, 'Dibuat otomatis dari Stok Opname') !== false ||
                        strpos($pNote, 'Dari mklbj') !== false ||
                        strpos($pNote, 'Dari inspecting') !== false ||
                        strpos($pNote, 'Hasil Pemotongan') !== false
                    ) {
                        continue;
                    }

                    // Cek jika pemotongan ID
                    if (preg_match('/Pemotongan ID:\s*(\d+)/i', $pNote, $matches)) {
                        $potongId = (int)$matches[1];
                        if (isset($recordedPotongIds[$potongId])) {
                            // Sudah dicatat di bagian 1, jangan duplikat!
                            continue;
                        }
                        $recordedPotongIds[$potongId] = true;
                        $potongModel = \common\models\ar\TrnPotongStock::findOne($potongId);
                        if ($potongModel && $potongModel->stock) {
                            $pItems = [];
                            foreach ($potongModel->trnPotongStockItems as $pItem) {
                                $pItems[] = (float)$pItem->qty;
                            }
                            $sumP = array_sum($pItems);
                            $currentSisa = (float)$potongModel->stock->qty;
                            $origQty = $currentSisa + $sumP;

                            $tglRaw = $potongModel->date ?: date('Y-m-d');
                            $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                            $noDoc = !empty($potongModel->no) ? 'No: ' . $potongModel->no : 'ID: ' . $potongId;

                            if ($currentSisa > 0) {
                                $descText = 'Qty Asal ' . $origQty . ' dipotong ' . implode(' + ', $pItems) . ' (Sisa Stock: ' . $currentSisa . ')';
                            } else {
                                $descText = 'Qty Asal ' . $origQty . ' dipotong habis menjadi ' . implode(' + ', $pItems);
                            }

                            $historyEntries[] = [
                                'date' => $tglRaw,
                                'text' => $tglFormatted . ' - PEMOTONGAN STOCK (' . $noDoc . '): ' . $descText
                            ];
                            continue;
                        }
                    }

                    // Cek jika catatan BARANG KELUAR
                    if (stripos($pNote, 'BARANG KELUAR') !== false) {
                        $tglRaw = !empty($oG['date']) ? $oG['date'] : (!empty($oG['updated_at']) ? date('Y-m-d', $oG['updated_at']) : date('Y-m-d'));
                        if (preg_match('/\(TGL\s*(\d{2}\/\d{2}\/\d{4})\)/i', $pNote, $mDate)) {
                            $dObj = \DateTime::createFromFormat('d/m/Y', $mDate[1]);
                            if ($dObj) {
                                $tglRaw = $dObj->format('Y-m-d');
                            }
                        }
                        $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                        // Bersihkan tulisan tanggal di dalam teks agar tidak redundant
                        $cleanText = preg_replace('/\(TGL\s*\d{2}\/\d{2}\/\d{4}\)/i', '', $pNote);
                        $cleanText = trim($cleanText);

                        $historyEntries[] = [
                            'date' => $tglRaw,
                            'text' => $tglFormatted . ' - ' . $cleanText
                        ];
                        continue;
                    }

                    // Catatan mutasi lainnya
                    $tglRaw = !empty($oG['date']) ? $oG['date'] : (!empty($oG['updated_at']) ? date('Y-m-d', $oG['updated_at']) : date('Y-m-d'));
                    $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                    $historyEntries[] = [
                        'date' => $tglRaw,
                        'text' => $tglFormatted . ' - ' . $pNote
                    ];
                }
            }

            // 3. Ambil data pengiriman buyer (Surat Jalan) dan kelompokkan per Surat Jalan agar tidak terpecah/duplikat
            $kirimBuyerItems = (new Query())
                ->select([
                    'header.id as header_id',
                    'header.no as no_sj',
                    'header.date as tgl_sj',
                    'header.nama_buyer',
                    'header.status as status_sj',
                    'gudang.qty',
                    'gudang.unit'
                ])
                ->from('trn_kirim_buyer_item item')
                ->innerJoin('trn_kirim_buyer kb', 'item.kirim_buyer_id = kb.id')
                ->innerJoin('trn_kirim_buyer_header header', 'kb.header_id = header.id')
                ->innerJoin('trn_gudang_jadi gudang', 'item.stock_id = gudang.id')
                ->where(['gudang.locs_code' => $subLocParam])
                ->all();

            $sjBuyerGroups = [];
            foreach ($kirimBuyerItems as $kbItem) {
                $sjKey = $kbItem['header_id'] . '_' . $kbItem['no_sj'];
                if (!isset($sjBuyerGroups[$sjKey])) {
                    $sjBuyerGroups[$sjKey] = [
                        'no_sj' => !empty($kbItem['no_sj']) ? $kbItem['no_sj'] : 'DRAFT',
                        'tgl_sj' => !empty($kbItem['tgl_sj']) ? $kbItem['tgl_sj'] : date('Y-m-d'),
                        'nama_buyer' => !empty($kbItem['nama_buyer']) ? trim($kbItem['nama_buyer']) : '',
                        'items' => [],
                        'total_qty' => 0,
                    ];
                }
                $qty = (float)$kbItem['qty'];
                $sjBuyerGroups[$sjKey]['items'][] = $qty;
                $sjBuyerGroups[$sjKey]['total_qty'] += $qty;
            }

            foreach ($sjBuyerGroups as $sjData) {
                $tglRaw = $sjData['tgl_sj'];
                $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                $buyerPart = !empty($sjData['nama_buyer']) ? ' BUYER: ' . $sjData['nama_buyer'] : '';
                $pcsCount = count($sjData['items']);
                $pcsListStr = implode(', ', $sjData['items']);

                $historyEntries[] = [
                    'date' => $tglRaw,
                    'text' => $tglFormatted . ' - STOCK KELUAR (PENGIRIMAN BUYER): NO SJ ' . $sjData['no_sj'] . $buyerPart . ' - Total ' . $sjData['total_qty'] . ' YD (' . $pcsCount . ' Pcs: ' . $pcsListStr . ' YD)'
                ];
            }

            // 4. Ambil data pengiriman makloon dan kelompokkan per dokumen
            $kirimMakloonItems = (new Query())
                ->select([
                    'km.id as km_id',
                    'km.no as no_doc',
                    'km.date as tgl_doc',
                    'gudang.qty',
                    'gudang.unit'
                ])
                ->from('trn_kirim_makloon_item item')
                ->innerJoin('trn_kirim_makloon km', 'item.kirim_makloon_id = km.id')
                ->innerJoin('trn_gudang_jadi gudang', 'item.stock_id = gudang.id')
                ->where(['gudang.locs_code' => $subLocParam])
                ->all();

            $makloonGroups = [];
            foreach ($kirimMakloonItems as $kmItem) {
                $kmKey = $kmItem['km_id'] . '_' . $kmItem['no_doc'];
                if (!isset($makloonGroups[$kmKey])) {
                    $makloonGroups[$kmKey] = [
                        'no_doc' => !empty($kmItem['no_doc']) ? $kmItem['no_doc'] : '-',
                        'tgl_doc' => !empty($kmItem['tgl_doc']) ? $kmItem['tgl_doc'] : date('Y-m-d'),
                        'items' => [],
                        'total_qty' => 0,
                    ];
                }
                $qty = (float)$kmItem['qty'];
                $makloonGroups[$kmKey]['items'][] = $qty;
                $makloonGroups[$kmKey]['total_qty'] += $qty;
            }

            foreach ($makloonGroups as $mGroup) {
                $tglRaw = $mGroup['tgl_doc'];
                $tglFormatted = date('d/m/Y', strtotime($tglRaw));
                $pcsCount = count($mGroup['items']);
                $pcsListStr = implode(', ', $mGroup['items']);

                $historyEntries[] = [
                    'date' => $tglRaw,
                    'text' => $tglFormatted . ' - STOCK KELUAR (KIRIM MAKLOON): NO ' . $mGroup['no_doc'] . ' - Total ' . $mGroup['total_qty'] . ' YD (' . $pcsCount . ' Pcs: ' . $pcsListStr . ' YD)'
                ];
            }

            // 5. Urutkan seluruh riwayat mutasi secara kronologis hari/tanggal (ASC)
            usort($historyEntries, function ($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
        }

        // Ambil daftar teks unik tanpa duplikasi
        $outNotes = [];
        foreach ($historyEntries as $entry) {
            $txt = trim($entry['text']);
            if (!empty($txt) && !in_array($txt, $outNotes)) {
                $outNotes[] = $txt;
            }
        }

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

