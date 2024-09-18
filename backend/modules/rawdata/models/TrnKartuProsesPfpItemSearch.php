<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKartuProsesPfpItem;

/**
 * TrnKartuProsesPfpItemSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnKartuProsesPfpItem`.
 */
class TrnKartuProsesPfpItemSearch extends TrnKartuProsesPfpItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'order_pfp_id', 'kartu_process_id', 'stock_id', 'panjang_m', 'tube', 'status', 'created_at'], 'integer'],
            [['mesin', 'note', 'date'], 'safe'],
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
        $query = TrnKartuProsesPfpItem::find();

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
            'order_pfp_id' => $this->order_pfp_id,
            'kartu_process_id' => $this->kartu_process_id,
            'stock_id' => $this->stock_id,
            'panjang_m' => $this->panjang_m,
            'tube' => $this->tube,
            'status' => $this->status,
            'date' => $this->date,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'mesin', $this->mesin])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
