<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGreigeKeluarItem;

/**
 * TrnGreigeKeluarItemSearch represents the model behind the search form of `common\models\ar\TrnGreigeKeluarItem`.
 */
class TrnGreigeKeluarItemSearch extends TrnGreigeKeluarItem
{
    public $dateRange;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_keluar_id', 'stock_greige_id'], 'integer'],
            [['note', 'dateRange'], 'safe'],
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
        $query = TrnGreigeKeluarItem::find()->joinWith('greigeKeluar');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_greige_keluar.date' => SORT_ASC],
            'desc' => ['trn_greige_keluar.date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_greige_keluar_item.greige_keluar_id' => $this->greige_keluar_id,
            'trn_greige_keluar_item.stock_greige_id' => $this->stock_greige_id,
        ]);

        $query->andFilterWhere(['ilike', 'trn_greige_keluar_item.note', $this->note]);

        return $dataProvider;
    }
}
