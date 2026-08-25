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
            [['opname_code', 'qr_code', 'qr_code_desc', 'unit', 'join_piece', 'locs_code', 'remark', 'dateRange'], 'safe'],
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
        $query = TrnGudangJadiOpnamePcs::find();

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
                $query->andFilterWhere(['between', 'trn_gudang_jadi_opname_pcs.created_at', $this->from_date, $this->to_date]);
            }
        }

        $query->andFilterWhere([
            'trn_gudang_jadi_opname_pcs.id' => $this->id,
            'trn_gudang_jadi_opname_pcs.id_trn_gudang_jadi' => $this->id_trn_gudang_jadi,
            'trn_gudang_jadi_opname_pcs.qty' => $this->qty,
            'trn_gudang_jadi_opname_pcs.grade' => $this->grade,
            'trn_gudang_jadi_opname_pcs.status' => $this->status,
            'trn_gudang_jadi_opname_pcs.created_at' => $this->created_at,
            'trn_gudang_jadi_opname_pcs.created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.opname_code', $this->opname_code])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.qr_code', $this->qr_code])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.qr_code_desc', $this->qr_code_desc])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.unit', $this->unit])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.join_piece', $this->join_piece])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.locs_code', $this->locs_code])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi_opname_pcs.remark', $this->remark]);

        return $dataProvider;
    }
}
