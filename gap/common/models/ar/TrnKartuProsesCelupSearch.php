<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesCelup;

/**
 * TrnKartuProsesCelupSearch represents the model behind the search form of `common\models\ar\TrnKartuProsesCelup`.
 */
class TrnKartuProsesCelupSearch extends TrnKartuProsesCelup
{
    public $orderCelupNo;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'reject_notes', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'gramasi', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling'], 'safe'],
            [['orderCelupNo', 'dateRange'], 'safe']
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
        $query = TrnKartuProsesCelup::find();
        $query->joinWith('orderCelup');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_kartu_proses_celup.date' => SORT_ASC],
            'desc' => ['trn_kartu_proses_celup.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderCelupNo'] = [
            'asc' => ['trn_order_celup.no' => SORT_ASC],
            'desc' => ['trn_order_celup.no' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kartu_proses_celup.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kartu_proses_celup.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kartu_proses_celup.id' => $this->id,
            'trn_kartu_proses_celup.greige_group_id' => $this->greige_group_id,
            'trn_kartu_proses_celup.greige_id' => $this->greige_id,
            'trn_kartu_proses_celup.order_celup_id' => $this->order_celup_id,
            'trn_kartu_proses_celup.no_urut' => $this->no_urut,
            'trn_kartu_proses_celup.asal_greige' => $this->asal_greige,
            'trn_kartu_proses_celup.date' => $this->date,
            'trn_kartu_proses_celup.posted_at' => $this->posted_at,
            'trn_kartu_proses_celup.approved_at' => $this->approved_at,
            'trn_kartu_proses_celup.approved_by' => $this->approved_by,
            'trn_kartu_proses_celup.status' => $this->status,
            'trn_kartu_proses_celup.created_at' => $this->created_at,
            'trn_kartu_proses_celup.created_by' => $this->created_by,
            'trn_kartu_proses_celup.updated_at' => $this->updated_at,
            'trn_kartu_proses_celup.updated_by' => $this->updated_by,
            'trn_kartu_proses_celup.delivered_at' => $this->delivered_at,
            'trn_kartu_proses_celup.delivered_by' => $this->delivered_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_celup.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.berat', $this->berat])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.lebar', $this->lebar])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.k_density_lusi', $this->k_density_lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.k_density_pakan', $this->k_density_pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.gramasi', $this->gramasi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.lebar_preset', $this->lebar_preset])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.lebar_finish', $this->lebar_finish])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.berat_finish', $this->berat_finish])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.t_density_lusi', $this->t_density_lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.t_density_pakan', $this->t_density_pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_celup.handling', $this->handling])
            ->andFilterWhere(['ilike', 'trn_order_celup.no', $this->orderCelupNo])
        ;

        return $dataProvider;
    }
}
