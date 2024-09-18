<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnMemoPerubahanData;

/**
 * TrnMemoPerubahanDataSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnMemoPerubahanData`.
 */
class TrnMemoPerubahanDataSearch extends TrnMemoPerubahanData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['description', 'date', 'no'], 'safe'],
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
        $query = TrnMemoPerubahanData::find();

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
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'no_urut' => $this->no_urut,
        ]);

        $query->andFilterWhere(['ilike', 'description', $this->description])
            ->andFilterWhere(['ilike', 'no', $this->no]);

        return $dataProvider;
    }
}
