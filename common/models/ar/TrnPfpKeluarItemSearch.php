<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnPfpKeluarItem;

/**
 * TrnPfpKeluarItemSearch represents the model behind the search form of `common\models\ar\TrnPfpKeluarItem`.
 */
class TrnPfpKeluarItemSearch extends TrnPfpKeluarItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pfp_keluar_id', 'stock_pfp_id'], 'integer'],
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
        $query = TrnPfpKeluarItem::find();

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
            'pfp_keluar_id' => $this->pfp_keluar_id,
            'stock_pfp_id' => $this->stock_pfp_id,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
