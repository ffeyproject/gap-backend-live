<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnMixedGreigeItem;

/**
 * TrnMixedGreigeItemSearch represents the model behind the search form of `common\models\ar\TrnMixedGreigeItem`.
 */
class TrnMixedGreigeItemSearch extends TrnMixedGreigeItem
{
    public $grigeName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mix_id', 'stock_greige_id'], 'integer'],
            [['grigeName'], 'safe'],
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
        $query = TrnMixedGreigeItem::find();
        $query->joinWith(['mix.greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['grigeName'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mix_id' => $this->mix_id,
            'stock_greige_id' => $this->stock_greige_id,
        ]);

        $query->andFilterWhere(['like', 'mst_greige.nama_kain', $this->grigeName]);

        return $dataProvider;
    }
}
