<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGudangJadiOpnamePcs;

/**
 * TrnGudangJadiOpnamePcsSearch represents the model behind the search form of `common\models\ar\TrnGudangJadiOpnamePcs`.
 */
class TrnGudangJadiOpnamePcsSearch extends TrnGudangJadiOpnamePcs
{
    public $woNo;
    public $scNo;
    public $marketingName;
    public $customerName;
    public $color;
    public $motif;

    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_trn_gudang_jadi', 'grade', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['qty'], 'number'],
            [
                [
                    'opname_code', 'qr_code', 'qr_code_desc', 'unit', 'join_piece', 
                    'locs_code', 'remark', 'dateRange', 'woNo', 'scNo', 
                    'marketingName', 'customerName', 'color', 'motif'
                ],
                'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TrnGudangJadiOpnamePcs::find()
            ->alias('t')
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id')
            ->leftJoin(['sc' => 'trn_sc'], 'sc_g.sc_id = sc.id')
            ->leftJoin(['mkt' => 'user'], 'sc.marketing_id = mkt.id')
            ->leftJoin(['cust' => 'mst_customer'], 'sc.cust_id = cust.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
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
            't.id' => $this->id,
            't.id_trn_gudang_jadi' => $this->id_trn_gudang_jadi,
            't.qty' => $this->qty,
            't.grade' => $this->grade,
            't.status' => $this->status,
            't.created_at' => $this->created_at,
            't.created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['ilike', 't.opname_code', $this->opname_code])
            ->andFilterWhere(['ilike', 't.qr_code', $this->qr_code])
            ->andFilterWhere(['ilike', 't.qr_code_desc', $this->qr_code_desc])
            ->andFilterWhere(['ilike', 't.unit', $this->unit])
            ->andFilterWhere(['ilike', 't.join_piece', $this->join_piece])
            ->andFilterWhere(['ilike', 't.locs_code', $this->locs_code])
            ->andFilterWhere(['ilike', 't.remark', $this->remark])
            ->andFilterWhere(['ilike', 'wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'cust.name', $this->customerName])
            ->andFilterWhere(['ilike', 'COALESCE(gj.color, \'\')', $this->color])
            ->andFilterWhere(['ilike', 'COALESCE(g_group.nama_kain, g_mst.nama_kain, t.qr_code_desc, \'\')', $this->motif]);

        return $dataProvider;
    }
}
