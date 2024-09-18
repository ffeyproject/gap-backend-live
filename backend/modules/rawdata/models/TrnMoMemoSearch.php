<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnMoMemo;

/**
 * TrnMoMemoSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnMoMemo`.
 */
class TrnMoMemoSearch extends TrnMoMemo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mo_id', 'created_at'], 'integer'],
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
        $query = TrnMoMemo::find();

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
            'mo_id' => $this->mo_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'memo', $this->memo]);

        return $dataProvider;
    }
}
