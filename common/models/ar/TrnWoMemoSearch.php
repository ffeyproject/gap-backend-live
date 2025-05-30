<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnWoMemo;

/**
 * TrnWoMemoSearch represents the model behind the search form of `common\models\ar\TrnWoMemo`.
 */
class TrnWoMemoSearch extends TrnWoMemo
{
    public $dateRange;

    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'created_at'], 'integer'],
            [['memo', 'no_urut', 'no', 'dateRange'], 'safe'],
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
        $query = TrnWoMemo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->dateRange) && strpos($this->dateRange, ' to ') !== false) {
            list($start, $end) = explode(' to ', $this->dateRange);

            // Convert string date (Y-m-d) ke timestamp unix
            $startTimestamp = strtotime($start . ' 00:00:00');
            $endTimestamp = strtotime($end . ' 23:59:59');

            $query->andFilterWhere(['between', 'created_at', $startTimestamp, $endTimestamp]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'wo_id' => $this->wo_id,
            'no_urut' => $this->no_urut,
        ]);

        $query
            ->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'memo', $this->memo]);

        return $dataProvider;
    }
}