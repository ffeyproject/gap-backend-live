<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKartuProsesPrinting;

/**
 * TrnKartuProsesPrintingSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnKartuProsesPrinting`.
 */
class TrnKartuProsesPrintingSearch extends TrnKartuProsesPrinting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'delivered_at', 'delivered_by', 'wo_color_id'], 'integer'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'memo_pg', 'memo_pg_no', 'reject_notes', 'kombinasi'], 'safe'],
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
        $query = TrnKartuProsesPrinting::find();

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
            'posted_at' => $this->posted_at,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'memo_pg_at' => $this->memo_pg_at,
            'memo_pg_by' => $this->memo_pg_by,
            'delivered_at' => $this->delivered_at,
            'delivered_by' => $this->delivered_by,
            'wo_color_id' => $this->wo_color_id,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'memo_pg', $this->memo_pg])
            ->andFilterWhere(['ilike', 'memo_pg_no', $this->memo_pg_no])
            ->andFilterWhere(['ilike', 'reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'kombinasi', $this->kombinasi]);

        return $dataProvider;
    }
}
