<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKartuProsesDyeing;

/**
 * TrnKartuProsesDyeingSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnKartuProsesDyeing`.
 */
class TrnKartuProsesDyeingSearch extends TrnKartuProsesDyeing
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'wo_color_id'], 'integer'],
            [['no', 'dikerjakan_oleh', 'lusi', 'pakan', 'nomor_kartu', 'note', 'date', 'reject_notes', 'memo_pg', 'memo_pg_no', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling', 'hasil_tes_gosok'], 'safe'],
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
        $query = TrnKartuProsesDyeing::find();

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
            'sc_id' => $this->sc_id,
            'sc_greige_id' => $this->sc_greige_id,
            'mo_id' => $this->mo_id,
            'wo_id' => $this->wo_id,
            'kartu_proses_id' => $this->kartu_proses_id,
            'no_urut' => $this->no_urut,
            'asal_greige' => $this->asal_greige,
            'date' => $this->date,
            'nomor_kartu' => $this->nomor_kartu,
            'posted_at' => $this->posted_at,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'delivered_at' => $this->delivered_at,
            'delivered_by' => $this->delivered_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'memo_pg_at' => $this->memo_pg_at,
            'memo_pg_by' => $this->memo_pg_by,
            'wo_color_id' => $this->wo_color_id,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'nomor_kartu', $this->nomor_kartu])
            ->andFilterWhere(['ilike', 'reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'memo_pg', $this->memo_pg])
            ->andFilterWhere(['ilike', 'memo_pg_no', $this->memo_pg_no])
            ->andFilterWhere(['ilike', 'berat', $this->berat])
            ->andFilterWhere(['ilike', 'lebar', $this->lebar])
            ->andFilterWhere(['ilike', 'k_density_lusi', $this->k_density_lusi])
            ->andFilterWhere(['ilike', 'k_density_pakan', $this->k_density_pakan])
            ->andFilterWhere(['ilike', 'lebar_preset', $this->lebar_preset])
            ->andFilterWhere(['ilike', 'lebar_finish', $this->lebar_finish])
            ->andFilterWhere(['ilike', 'berat_finish', $this->berat_finish])
            ->andFilterWhere(['ilike', 't_density_lusi', $this->t_density_lusi])
            ->andFilterWhere(['ilike', 't_density_pakan', $this->t_density_pakan])
            ->andFilterWhere(['ilike', 'handling', $this->handling])
            ->andFilterWhere(['ilike', 'hasil_tes_gosok', $this->hasil_tes_gosok]);

        return $dataProvider;
    }
}