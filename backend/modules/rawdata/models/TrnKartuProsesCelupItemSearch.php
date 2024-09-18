<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKartuProsesCelupItem;

/**
 * TrnKartuProsesCelupItemSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnKartuProsesCelupItem`.
 */
class TrnKartuProsesCelupItemSearch extends TrnKartuProsesCelupItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'order_celup_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status'], 'integer'],
            [['mesin', 'note'], 'safe'],
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
        $query = TrnKartuProsesCelupItem::find();

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
            'order_celup_id' => $this->order_celup_id,
            'kartu_process_id' => $this->kartu_process_id,
            'stock_id' => $this->stock_id,
            'panjang_m' => $this->panjang_m,
            'tube' => $this->tube,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'mesin', $this->mesin])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
