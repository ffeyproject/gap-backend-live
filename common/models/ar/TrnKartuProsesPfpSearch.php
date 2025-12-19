<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesPfp;

/**
 * TrnKartuProsesPfpSearch represents the model behind the search form of `common\models\ar\TrnKartuProsesPfp`.
 */
class TrnKartuProsesPfpSearch extends TrnKartuProsesPfp
{
    public $orderPfpNo;
    public $dateRange;
    public $namaKain;
    private $from_date;
    private $to_date;
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'order_pfp_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'reject_notes', 'pc_bukaan', 'pc_scouring', 'pc_relaxing', 'pc_scutcher', 'pc_preset', 'pc_weight_reducetion', 'pc_washing_off', 'pc_heat_sett', 'pc_padding', 'nomor_kartu', 'namaKain'], 'safe'],
            [['orderPfpNo', 'dateRange'], 'safe']
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
        $query = TrnKartuProsesPfp::find();
        $query->joinWith('orderPfp', 'greige');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['namaKain'] = [
            'asc'  => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_kartu_proses_pfp.date' => SORT_ASC],
            'desc' => ['trn_kartu_proses_pfp.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderPfpNo'] = [
            'asc' => ['trn_order_pfp.no' => SORT_ASC],
            'desc' => ['trn_order_pfp.no' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kartu_proses_pfp.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kartu_proses_pfp.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kartu_proses_pfp.id' => $this->id,
            'trn_kartu_proses_pfp.greige_group_id' => $this->greige_group_id,
            'trn_kartu_proses_pfp.greige_id' => $this->greige_id,
            'trn_kartu_proses_pfp.order_pfp_id' => $this->order_pfp_id,
            'trn_kartu_proses_pfp.no_urut' => $this->no_urut,
            'trn_kartu_proses_pfp.asal_greige' => $this->asal_greige,
            'trn_kartu_proses_pfp.date' => $this->date,
            'trn_kartu_proses_pfp.posted_at' => $this->posted_at,
            'trn_kartu_proses_pfp.approved_at' => $this->approved_at,
            'trn_kartu_proses_pfp.approved_by' => $this->approved_by,
            'trn_kartu_proses_pfp.status' => $this->status,
            'trn_kartu_proses_pfp.created_at' => $this->created_at,
            'trn_kartu_proses_pfp.created_by' => $this->created_by,
            'trn_kartu_proses_pfp.updated_at' => $this->updated_at,
            'trn_kartu_proses_pfp.updated_by' => $this->updated_by,
            'trn_kartu_proses_pfp.delivered_at' => $this->delivered_at,
            'trn_kartu_proses_pfp.delivered_by' => $this->delivered_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.nomor_kartu', $this->nomor_kartu])
            //->andFilterWhere(['ilike', 'pc_bukaan', $this->pc_bukaan])
            //->andFilterWhere(['ilike', 'pc_scouring', $this->pc_scouring])
            //->andFilterWhere(['ilike', 'pc_relaxing', $this->pc_relaxing])
            //->andFilterWhere(['ilike', 'pc_scutcher', $this->pc_scutcher])
            //->andFilterWhere(['ilike', 'pc_preset', $this->pc_preset])
            //->andFilterWhere(['ilike', 'pc_weight_reducetion', $this->pc_weight_reducetion])
            //->andFilterWhere(['ilike', 'pc_washing_off', $this->pc_washing_off])
            //->andFilterWhere(['ilike', 'pc_heat_sett', $this->pc_heat_sett])
            //->andFilterWhere(['ilike', 'pc_padding', $this->pc_padding])
            ->andFilterWhere(['ilike', 'trn_order_pfp.no', $this->orderPfpNo])
        ;

        $query->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->namaKain]);

        return $dataProvider;
    }
}