<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnScAgen;

/**
 * TrnScAgenSearch represents the model behind the search form of `common\models\ar\TrnScAgen`.
 */
class TrnScAgenSearch extends TrnScAgen
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'no_urut'], 'integer'],
            [['date', 'nama_agen', 'attention', 'no'], 'safe'],
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
        $query = TrnScAgen::find();

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
            'date' => $this->date,
            'no_urut' => $this->no_urut,
        ]);

        $query->andFilterWhere(['ilike', 'nama_agen', $this->nama_agen])
            ->andFilterWhere(['ilike', 'attention', $this->attention])
            ->andFilterWhere(['ilike', 'no', $this->no]);

        return $dataProvider;
    }
}
