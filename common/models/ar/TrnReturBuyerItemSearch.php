<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnReturBuyerItem;

/**
 * TrnReturBuyerItemSearch represents the model behind the search form of `common\models\ar\TrnReturBuyerItem`.
 */
class TrnReturBuyerItemSearch extends TrnReturBuyerItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'retur_buyer_id', 'qty'], 'integer'],
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
        $query = TrnReturBuyerItem::find();

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
            'retur_buyer_id' => $this->retur_buyer_id,
            'qty' => $this->qty,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
