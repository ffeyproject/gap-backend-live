<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKartuProsesCelup;

/**
 * TrnKartuProsesCelupSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnKartuProsesCelup`.
 */
class TrnKartuProsesCelupSearch extends TrnKartuProsesCelup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'reject_notes', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'gramasi', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling'], 'safe'],
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

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'greige_group_id' => $this->greige_group_id,
            'greige_id' => $this->greige_id,
            'order_celup_id' => $this->order_celup_id,
            'no_urut' => $this->no_urut,
            'asal_greige' => $this->asal_greige,
            'date' => $this->date,
            'posted_at' => $this->posted_at,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'delivered_at' => $this->delivered_at,
            'delivered_by' => $this->delivered_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'berat', $this->berat])
            ->andFilterWhere(['ilike', 'lebar', $this->lebar])
            ->andFilterWhere(['ilike', 'k_density_lusi', $this->k_density_lusi])
            ->andFilterWhere(['ilike', 'k_density_pakan', $this->k_density_pakan])
            ->andFilterWhere(['ilike', 'gramasi', $this->gramasi])
            ->andFilterWhere(['ilike', 'lebar_preset', $this->lebar_preset])
            ->andFilterWhere(['ilike', 'lebar_finish', $this->lebar_finish])
            ->andFilterWhere(['ilike', 'berat_finish', $this->berat_finish])
            ->andFilterWhere(['ilike', 't_density_lusi', $this->t_density_lusi])
            ->andFilterWhere(['ilike', 't_density_pakan', $this->t_density_pakan])
            ->andFilterWhere(['ilike', 'handling', $this->handling]);

        return $dataProvider;
    }
}
