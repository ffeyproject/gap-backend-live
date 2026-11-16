<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MstK3lSearch represents the model behind the search form of `common\models\ar\MstK3l`.
 */
class MstK3lSearch extends MstK3l
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['k3l_code', 'k3l_desc', 'k3l_active', 'k3l_add_date', 'k3l_upd_date'], 'safe'],
            [['k3l_add_by', 'k3l_upd_by'], 'integer'],
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
        $query = MstK3l::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'k3l_code' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'k3l_active' => $this->k3l_active,
            'k3l_add_by' => $this->k3l_add_by,
            'k3l_upd_by' => $this->k3l_upd_by,
        ]);

        $query->andFilterWhere(['ilike', 'k3l_code', $this->k3l_code])
            ->andFilterWhere(['ilike', 'k3l_desc', $this->k3l_desc]);

        if (!empty($this->k3l_add_date)) {
            $query->andFilterWhere(['like', 'k3l_add_date::text', $this->k3l_add_date]);
        }

        if (!empty($this->k3l_upd_date)) {
            $query->andFilterWhere(['like', 'k3l_upd_date::text', $this->k3l_upd_date]);
        }

        return $dataProvider;
    }
}
