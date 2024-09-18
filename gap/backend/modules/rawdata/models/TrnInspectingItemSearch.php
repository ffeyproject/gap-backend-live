<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnInspectingItem;

/**
 * TrnInspectingItemSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnInspectingItem`.
 */
class TrnInspectingItemSearch extends TrnInspectingItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'qty', 'trn_inspecting_roll_id'], 'integer'],
            [['no', 'note'], 'safe'],
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
        $query = TrnInspectingItem::find();

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
            'qty' => $this->qty,
            'trn_inspecting_roll_id' => $this->trn_inspecting_roll_id,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
