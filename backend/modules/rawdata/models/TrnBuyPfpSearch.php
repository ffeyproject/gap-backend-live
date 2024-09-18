<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnBuyPfp;

/**
 * TrnBuyPfpSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnBuyPfp`.
 */
class TrnBuyPfpSearch extends TrnBuyPfp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'integer'],
            [['no_document', 'vendor', 'note', 'date', 'reject_note'], 'safe'],
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
        $query = TrnBuyPfp::find();

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
            'status' => $this->status,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'approval_id' => $this->approval_id,
            'approval_time' => $this->approval_time,
        ]);

        $query->andFilterWhere(['ilike', 'no_document', $this->no_document])
            ->andFilterWhere(['ilike', 'vendor', $this->vendor])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'reject_note', $this->reject_note]);

        return $dataProvider;
    }
}
