<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnMemoRepair;

/**
 * TrnMemoRepairSearch represents the model behind the search form of `common\models\ar\TrnMemoRepair`.
 */
class TrnMemoRepairSearch extends TrnMemoRepair
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $returBuyerNo;
    public $scNo;
    public $moNo;
    public $woNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'retur_buyer_id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'mutasi_at', 'mutasi_by'], 'integer'],
            [['date', 'no', 'note', 'mutasi_note', 'dateRange', 'scNo', 'moNo', 'woNo', 'returBuyerNo'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = TrnMemoRepair::find();
        $query->joinWith(['returBuyer', 'wo', 'mo', 'sc']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['moNo'] = [
            'asc' => ['trn_mo.no' => SORT_ASC],
            'desc' => ['trn_mo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['returBuyerNo'] = [
            'asc' => ['trn_retur_buyer.no' => SORT_ASC],
            'desc' => ['trn_retur_buyer.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_memo_repair.date' => SORT_ASC],
            'desc' => ['trn_memo_repair.date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_memo_repair.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_memo_repair.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_memo_repair.id' => $this->id,
            'trn_memo_repair.retur_buyer_id' => $this->retur_buyer_id,
            'trn_memo_repair.sc_id' => $this->sc_id,
            'trn_memo_repair.sc_greige_id' => $this->sc_greige_id,
            'trn_memo_repair.mo_id' => $this->mo_id,
            'trn_memo_repair.wo_id' => $this->wo_id,
            'trn_memo_repair.date' => $this->date,
            'trn_memo_repair.no_urut' => $this->no_urut,
            'trn_memo_repair.status' => $this->status,
            'trn_memo_repair.created_at' => $this->created_at,
            'trn_memo_repair.created_by' => $this->created_by,
            'trn_memo_repair.updated_at' => $this->updated_at,
            'trn_memo_repair.updated_by' => $this->updated_by,
            'trn_memo_repair.mutasi_at' => $this->mutasi_at,
            'trn_memo_repair.mutasi_by' => $this->mutasi_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_memo_repair.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_memo_repair.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_memo_repair.mutasi_note', $this->mutasi_note])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'trn_retur_buyer.no', $this->returBuyerNo])
        ;

        return $dataProvider;
    }
}
