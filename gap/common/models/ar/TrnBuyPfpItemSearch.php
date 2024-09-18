<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnBuyPfpItem;

/**
 * TrnBuyPfpItemSearch represents the model behind the search form of `common\models\ar\TrnBuyPfpItem`.
 */
class TrnBuyPfpItemSearch extends TrnBuyPfpItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'buy_pfp_id', 'panjang_m'], 'integer'],
            [['note'], 'safe'],
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
        $query = TrnBuyPfpItem::find();

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
            'buy_pfp_id' => $this->buy_pfp_id,
            'panjang_m' => $this->panjang_m,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
