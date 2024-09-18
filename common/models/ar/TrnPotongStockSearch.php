<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnPotongStock;

/**
 * TrnPotongStockSearch represents the model behind the search form of `common\models\ar\TrnPotongStock`.
 */
class TrnPotongStockSearch extends TrnPotongStock
{
    public $dateRange;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stock_id', 'no_urut', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['no', 'note', 'date', 'diperintahkan_oleh', 'dateRange'], 'safe'],
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
        $query = TrnPotongStock::find();

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
            'asc' => ['trn_potong_stock.date' => SORT_ASC],
            'desc' => ['trn_potong_stock.date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $from_date = substr($this->dateRange, 0, 10);
            $to_date = substr($this->dateRange, 14);

            if($from_date == $to_date){
                $query->andFilterWhere(['trn_potong_stock.date' => $from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_potong_stock.date', $from_date, $to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stock_id' => $this->stock_id,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'diperintahkan_oleh', $this->diperintahkan_oleh]);

        return $dataProvider;
    }
}
