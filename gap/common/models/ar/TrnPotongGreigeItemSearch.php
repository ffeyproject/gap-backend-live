<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnPotongGreigeItem;

/**
 * TrnPotongGreigeItemSearch represents the model behind the search form of `common\models\ar\TrnPotongGreigeItem`.
 */
class TrnPotongGreigeItemSearch extends TrnPotongGreigeItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'potong_greige_id', 'stock_greige_id'], 'integer'],
            ['panjang_m', 'number'],
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
        $query = TrnPotongGreigeItem::find();

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
            'potong_greige_id' => $this->potong_greige_id,
            'stock_greige_id' => $this->stock_greige_id,
            'panjang_m' => $this->panjang_m,
        ]);

        return $dataProvider;
    }
}
