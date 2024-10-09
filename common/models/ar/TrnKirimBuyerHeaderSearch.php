<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKirimBuyerHeader;

/**
 * TrnKirimBuyerHeaderSearch represents the model behind the search form of `common\models\ar\TrnKirimBuyerHeader`.
 */
class TrnKirimBuyerHeaderSearch extends TrnKirimBuyerHeader
{
    public $from_date;
    public $to_date;
    public $dateRange;
    public $custName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['pengirim', 'penerima', 'note', 'dateRange', 'custName','nama_buyer'], 'safe'],
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
        $query = TrnKirimBuyerHeader::find();
        $query->joinWith('customer');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['custName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_kirim_buyer_header.date' => SORT_ASC],
            'desc' => ['trn_kirim_buyer_header.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nama_buyer'] = [
            'asc' => ['trn_kirim_buyer_header.nama_buyer' => SORT_ASC],
            'desc' => ['trn_kirim_buyer_header.nama_buyer' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_kirim_buyer_header.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kirim_buyer_header.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kirim_buyer_header.id' => $this->id,
            'trn_kirim_buyer_header.customer_id' => $this->customer_id,
            'trn_kirim_buyer_header.status' => $this->status,
            'trn_kirim_buyer_header.created_at' => $this->created_at,
            'trn_kirim_buyer_header.created_by' => $this->created_by,
            'trn_kirim_buyer_header.updated_at' => $this->updated_at,
            'trn_kirim_buyer_header.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_buyer_header.pengirim', $this->pengirim])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer_header.penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer_header.note', $this->note])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->custName])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer_header.nama_buyer', $this->nama_buyer])
        ;

        return $dataProvider;
    }
}
