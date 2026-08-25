<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * StokOpnameGudangJadiRekapSearch represents the search model for Rekap Stok Opname Gudang Jadi.
 */
class StokOpnameGudangJadiRekapSearch extends Model
{
    public $opname_code;
    public $locs_code;
    public $motif;
    public $color;
    public $grade;
    public $status;
    public $unit;
    public $dateRange;

    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grade', 'status'], 'integer'],
            [['opname_code', 'locs_code', 'motif', 'color', 'unit', 'dateRange'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance for Rekap Stok Opname with grouping by Motif, Color, Opname Code, Location, Grade & Status.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())
            ->select([
                'opname_code' => 't.opname_code',
                'locs_code' => 't.locs_code',
                'motif' => 'COALESCE(g_group.nama_kain, g_mst.nama_kain, t.qr_code_desc, \'-\')',
                'color' => 'COALESCE(gj.color, \'-\')',
                'grade' => 't.grade',
                'status' => 't.status',
                'unit' => 't.unit',
                'total_pcs' => 'COUNT(t.id)',
                'total_qty' => 'SUM(t.qty)',
            ])
            ->from(['t' => 'trn_gudang_jadi_opname_pcs'])
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id')
            ->groupBy([
                't.opname_code',
                't.locs_code',
                'COALESCE(g_group.nama_kain, g_mst.nama_kain, t.qr_code_desc, \'-\')',
                'COALESCE(gj.color, \'-\')',
                't.grade',
                't.status',
                't.unit',
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'opname_code',
                    'locs_code',
                    'motif',
                    'color',
                    'grade',
                    'status',
                    'total_pcs',
                    'total_qty',
                ],
                'defaultOrder' => [
                    'opname_code' => SORT_ASC,
                    'motif' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = strtotime(substr($this->dateRange, 0, 10) . ' 00:00:00');
            $this->to_date = strtotime(substr($this->dateRange, 14) . ' 23:59:59');

            if ($this->from_date && $this->to_date) {
                $query->andFilterWhere(['between', 't.created_at', $this->from_date, $this->to_date]);
            }
        }

        $query->andFilterWhere([
            't.grade' => $this->grade,
            't.status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 't.opname_code', $this->opname_code])
            ->andFilterWhere(['ilike', 't.locs_code', $this->locs_code])
            ->andFilterWhere(['ilike', 't.unit', $this->unit])
            ->andFilterWhere(['ilike', 'COALESCE(gj.color, \'-\')', $this->color])
            ->andFilterWhere(['ilike', 'COALESCE(g_group.nama_kain, g_mst.nama_kain, t.qr_code_desc, \'-\')', $this->motif]);

        return $dataProvider;
    }
}
