<?php

namespace backend\controllers;

use common\models\ar\{ TrnInspecting, InspectingMklBj, TrnGudangJadi, TrnGudangJadiSearch };
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
     * Lists all TrnGudangJadi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $limit = Yii::$app->request->get('limit', 50);
        $timestamp = date('l, d F Y H:i:s');
        $searchModel = Yii::$app->request->getQueryParams();

        if (empty($searchModel['sub_location'])) {
            $results = [];
        } else {
            $query = new Query();

            $query->select([
                'trn_wo.no no_wo',
                'mst_greige.nama_kain mst_greige_nama_kain',
                'mst_greige_group.nama_kain mst_greige_group_nama_kain',
                'trn_sc_greige.process sc_greige_jenis_proses',
                'trn_mo.process trn_mo_jenis_proses',
                'trn_mo.article trn_mo_article',
                'trn_mo.design trn_mo_design',
                'trn_sc_greige.lebar_kain sc_greige_lebar_kain',
                'trn_gudang_jadi.unit',
                'trn_gudang_jadi.grade',
                'trn_gudang_jadi.source',
                'trn_gudang_jadi.source_ref',
                'trn_gudang_jadi.status',
                'trn_gudang_jadi.color',
                'trn_gudang_jadi.jenis_gudang',
                'trn_gudang_jadi.qty',
                'trn_gudang_jadi.status'
            ])
            ->leftJoin('trn_wo', 'trn_gudang_jadi.wo_id = trn_wo.id')
            ->leftJoin('trn_mo', 'trn_wo.mo_id = trn_mo.id')
            ->leftJoin('trn_sc_greige', 'trn_wo.sc_greige_id = trn_sc_greige.id')
            ->leftJoin('mst_greige', 'trn_wo.greige_id = mst_greige.id')
            ->leftJoin('mst_greige_group', 'trn_sc_greige.greige_group_id = mst_greige_group.id')
            ->where(['=', 'trn_gudang_jadi.status', 1])
            ->from('trn_gudang_jadi');
    
            if (!empty($searchModel['sub_location'])) {
                $query->andWhere(['=', 'trn_gudang_jadi.locs_code', $searchModel['sub_location']]);
            }

            $result = $query->all();

            $final_result = []; $no_wo_map = []; 
            foreach ($result as $result) {
                if ($result['source'] == 1) { // 1 == SOURCE_PACKING
                    $getTableData = (new \yii\db\Query())
                        ->from(TrnInspecting::tableName())
                        ->select('*')
                        ->where(['no' => $result['source_ref']])
                        ->one();

                    if (!$getTableData) {
                        $getTableData = (new \yii\db\Query())
                            ->from(InspectingMklBj::tableName())
                            ->select('*')
                            ->where(['no' => $result['source_ref']])
                            ->one();
                    }

                    $processType = $getTableData['jenis_process'] ?? $getTableData['jenis'];
                } else {
                    $processType = $result['trn_mo_jenis_proses'];
                }

                if ($processType == 1) { // 1 == dyeing
                    $is_design_or_atikel = $result['trn_mo_article'];
                } else { // 2 == printing && // 3 == pfp
                    $articleIsNotNull = $result['trn_mo_article'] ? '/' : '';
                    $is_design_or_atikel = $result['trn_mo_article'] . $articleIsNotNull . $result['trn_mo_design'];
                }

                // manipulate the array

                $no_wo = $result['no_wo'];
                $unit = $result['unit'];
                $qty = $result['qty'];
                $color = $result['color'];
                $grade = $result['grade'];

                if (isset($no_wo_map[$no_wo])) {
                    // Cek apakah warna sudah ada di dalam array warna untuk WO ini
                    if (isset($final_result[$no_wo_map[$no_wo]]['colors'][$color])) {
                        $final_result[$no_wo_map[$no_wo]]['colors'][$color]['qty'][] = ['qty' => $qty, 'grade' => $grade, 'unit' => $unit];
                        $final_result[$no_wo_map[$no_wo]]['colors'][$color]['total_qty'] += $qty;
                    } else {
                        // Jika warna belum ada, tambahkan entri baru untuk warna ini
                        $final_result[$no_wo_map[$no_wo]]['colors'][$color] = [
                            'qty' => [['qty' => $qty, 'grade' => $grade,'unit' => $unit]],  
                            'total_qty' => $qty,
                        ];
                    }

                } else {
                    // Jika nomor WO belum ada, buat entri baru
                    $final_result[] = [
                        'unit' => $unit,
                        'no_wo' => $no_wo,
                        'design' => $is_design_or_atikel,
                        'colors' => [
                            $color => [
                                'qty' => [['qty' => $qty, 'grade' => $grade, 'unit' => $unit]],
                                'total_qty' => $qty,    
                            ]
                        ]
                    ];
                    $no_wo_map[$no_wo] = count($final_result) - 1;
                }
            }
            $results = $final_result;
            // print("<pre>".print_r($results,true)."</pre>");
            // die;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => [
                'pageSize' => $limit, // Number of items per page
            ],
            // You can also configure sorting, filtering, and other options here
        ]);

        // var_dump(count($dataProvider->models));
        // die;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'title' => !empty($searchModel['sub_location']) ? $searchModel['sub_location'] : '-',
            'timestamp' => $timestamp
        ]);
    }

}
