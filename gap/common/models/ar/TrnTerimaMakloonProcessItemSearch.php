<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnTerimaMakloonProcessItem;

/**
 * TrnTerimaMakloonProcessItemSearch represents the model behind the search form of `common\models\ar\TrnTerimaMakloonProcessItem`.
 */
class TrnTerimaMakloonProcessItemSearch extends TrnTerimaMakloonProcessItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'terima_makloon_id', 'qty'], 'integer'],
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
        $query = TrnTerimaMakloonProcessItem::find();

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
            'terima_makloon_id' => $this->terima_makloon_id,
            'qty' => $this->qty,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
