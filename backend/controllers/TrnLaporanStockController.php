<?php

namespace backend\controllers;

use common\models\ar\{ TrnGudangJadi, TrnGudangJadiSearch };
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\db\Expression;
use yii\data\ArrayDataProvider;

/**
 * TrnLaporanStockController implements the R actions for TrnGudangJadi model.
 */
class TrnLaporanStockController extends Controller
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
        // $startDate = Yii::$app->request->get('startDate') ? strtotime(Yii::$app->request->get('startDate').' 00:00:00') : strtotime(date('Y-m-d 00:00:00'));
        // $endDate = Yii::$app->request->get('endDate') ? strtotime(Yii::$app->request->get('endDate').' 23:59:59') : strtotime(date('Y-m-d 23:59:59'));
        $limit = Yii::$app->request->get('limit', 50);
        
        $query = new Query();
        $searchModel = Yii::$app->request->getQueryParams();
        $query->select([
            // 'trn_wo.no no_wo',
            'mst_greige.nama_kain mst_greige_nama_kain',
            'mst_greige_group.nama_kain mst_greige_group_nama_kain',
            'trn_sc_greige.process sc_greige_jenis_proses',
            'trn_sc_greige.lebar_kain sc_greige_lebar_kain',
            'trn_gudang_jadi.unit',
            // 'trn_gudang_jadi.created_at',
            // 'trn_gudang_jadi.id',
            'trn_gudang_jadi.grade',
            // 'trn_gudang_jadi.qty',
            'trn_gudang_jadi.status',
            'trn_gudang_jadi.color',
            'trn_gudang_jadi.jenis_gudang',
            // 'trn_gudang_jadi.source',
            'grade_a' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '1' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_b' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '2' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_c' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '3' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_d' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '4' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_e' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '5' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_ng' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '6' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_a_plus' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '7' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'grade_a_asterisk' => new Expression("SUM(CASE WHEN trn_gudang_jadi.grade = '8' THEN trn_gudang_jadi.qty ELSE '0' END)"),
            'total_qty' => new Expression("SUM(trn_gudang_jadi.qty)"), // New column for total sum of qty
            // 'converted_created_at' => new Expression("TO_TIMESTAMP(trn_gudang_jadi.created_at)::TIMESTAMPTZ AT TIME ZONE 'Asia/Bangkok'"),
        ])
        ->leftJoin('trn_wo', 'trn_gudang_jadi.wo_id = trn_wo.id')
        ->leftJoin('trn_sc_greige', 'trn_wo.sc_greige_id = trn_sc_greige.id')
        ->leftJoin('mst_greige', 'trn_wo.greige_id = mst_greige.id')
        ->leftJoin('mst_greige_group', 'trn_sc_greige.greige_group_id = mst_greige_group.id')
        // ->andWhere(['>=', 'trn_gudang_jadi.created_at', $startDate])
        // ->andWhere(['<=', 'trn_gudang_jadi.created_at', $endDate])
        ->groupBy([
            'mst_greige.nama_kain',
            'mst_greige_group.nama_kain',
            'trn_sc_greige.lebar_kain',
            'trn_gudang_jadi.unit',
            'trn_gudang_jadi.grade',
            'trn_gudang_jadi.color',
            // 'trn_gudang_jadi.qty',
            'trn_gudang_jadi.status',
            'trn_gudang_jadi.jenis_gudang',
            'trn_sc_greige.process',
            // 'trn_gudang_jadi.source',
        ])
        ->from('trn_gudang_jadi');

        if (!empty($searchModel['jenis_gudang'])) {
            $query->andWhere(['=', 'trn_gudang_jadi.jenis_gudang', $searchModel['jenis_gudang']]);
        }

        if (!empty($searchModel['grade'])) {
            $query->andWhere(['=', 'trn_gudang_jadi.grade', $searchModel['grade']]);
        }

        if (!empty($searchModel['status'])) {
            $query->andWhere(['=', 'trn_gudang_jadi.status', $searchModel['status']]);
        }

        if (!empty($searchModel['unit'])) {
            $query->andWhere(['=', 'trn_gudang_jadi.unit', $searchModel['unit']]);
        }

        if (!empty($searchModel['motif'])) {
            $searchTerm = strtolower($searchModel['motif']);
            $query->andWhere(new Expression('LOWER(mst_greige.nama_kain) LIKE :motif', [':motif' => '%' . $searchTerm . '%']));
            // $query->andWhere(new Expression('LOWER(mst_greige.nama_kain) = :motif', [':motif' => $searchTerm]));
        }

        if (!empty($searchModel['color'])) {
            $color = strtolower($searchModel['color']);
            $query->andWhere(new Expression('LOWER(trn_gudang_jadi.color) = :color', [':color' => $color]));
        }

        $results = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => [
                'pageSize' => $limit, // Number of items per page
            ],
            // You can also configure sorting, filtering, and other options here
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
