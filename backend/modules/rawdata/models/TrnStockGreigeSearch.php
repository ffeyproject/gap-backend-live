<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnStockGreige;

/**
 * TrnStockGreigeSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnStockGreige`.
 */
class TrnStockGreigeSearch extends TrnStockGreige
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'asal_greige', 'grade', 'panjang_m', 'status_tsd', 'status', 'jenis_gudang', 'created_at', 'created_by', 'updated_at', 'updated_by', 'keputusan_qc'], 'integer'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_set_lusi', 'no_document', 'pengirim', 'mengetahui', 'note', 'date', 'nomor_wo', 'color'], 'safe'],
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
        $query = TrnStockGreige::find();

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
            'asal_greige' => $this->asal_greige,
            'grade' => $this->grade,
            'panjang_m' => $this->panjang_m,
            'status_tsd' => $this->status_tsd,
            'status' => $this->status,
            'date' => $this->date,
            'jenis_gudang' => $this->jenis_gudang,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'keputusan_qc' => $this->keputusan_qc,
        ]);

        $query->andFilterWhere(['ilike', 'no_lapak', $this->no_lapak])
            ->andFilterWhere(['ilike', 'lot_lusi', $this->lot_lusi])
            ->andFilterWhere(['ilike', 'lot_pakan', $this->lot_pakan])
            ->andFilterWhere(['ilike', 'no_set_lusi', $this->no_set_lusi])
            ->andFilterWhere(['ilike', 'no_document', $this->no_document])
            ->andFilterWhere(['ilike', 'pengirim', $this->pengirim])
            ->andFilterWhere(['ilike', 'mengetahui', $this->mengetahui])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'nomor_wo', $this->nomor_wo])
            ->andFilterWhere(['ilike', 'color', $this->color]);

        return $dataProvider;
    }
}
