<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnInspecting;

/**
 * TrnInspectingSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnInspecting`.
 */
class TrnInspectingSearch extends TrnInspecting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_dyeing_id', 'jenis_process', 'no_urut', 'status', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'kartu_process_printing_id', 'memo_repair_id'], 'integer'],
            [['no', 'date', 'tanggal_inspeksi', 'no_lot', 'kombinasi', 'note', 'approval_reject_note', 'delivery_reject_note'], 'safe'],
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
        $query = TrnInspecting::find();

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
            'kartu_process_dyeing_id' => $this->kartu_process_dyeing_id,
            'jenis_process' => $this->jenis_process,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'tanggal_inspeksi' => $this->tanggal_inspeksi,
            'status' => $this->status,
            'unit' => $this->unit,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'delivered_at' => $this->delivered_at,
            'delivered_by' => $this->delivered_by,
            'kartu_process_printing_id' => $this->kartu_process_printing_id,
            'memo_repair_id' => $this->memo_repair_id,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'kombinasi', $this->kombinasi])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'approval_reject_note', $this->approval_reject_note])
            ->andFilterWhere(['ilike', 'delivery_reject_note', $this->delivery_reject_note]);

        return $dataProvider;
    }
}
