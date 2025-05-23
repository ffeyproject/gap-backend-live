<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnWoMemo;

/**
 * TrnWoMemoSearch represents the model behind the search form of `common\models\ar\TrnWoMemo`.
 */
class TrnWoMemoSearch extends TrnWoMemo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'created_at'], 'integer'],
            [['memo'], 'safe'],
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
        $query = TrnWoMemo::find();

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
            'wo_id' => $this->wo_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'memo', $this->memo]);

        return $dataProvider;
    }
}
