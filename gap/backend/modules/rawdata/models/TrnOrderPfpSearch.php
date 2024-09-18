<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnOrderPfp;

/**
 * TrnOrderPfpSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnOrderPfp`.
 */
class TrnOrderPfpSearch extends TrnOrderPfp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'approved_by', 'approved_at', 'proses_sampai'], 'integer'],
            [['no', 'note', 'date', 'approval_note', 'dasar_warna'], 'safe'],
            [['qty'], 'number'],
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
        $query = TrnOrderPfp::find();

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
            'no_urut' => $this->no_urut,
            'qty' => $this->qty,
            'status' => $this->status,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'handling_id' => $this->handling_id,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'proses_sampai' => $this->proses_sampai,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'approval_note', $this->approval_note])
            ->andFilterWhere(['ilike', 'dasar_warna', $this->dasar_warna]);

        return $dataProvider;
    }
}
