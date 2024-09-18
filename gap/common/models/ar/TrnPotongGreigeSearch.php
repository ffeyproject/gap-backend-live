<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnPotongGreige;

/**
 * TrnPotongGreigeSearch represents the model behind the search form of `common\models\ar\TrnPotongGreige`.
 */
class TrnPotongGreigeSearch extends TrnPotongGreige
{
    public $greigeGroupNamaKain;
    public $greigeNamaKain;
    public $dateRange;
    private $from_date;
    private $to_date;
    public $stockNoDocument;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stock_greige_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'note', 'date', 'stockNoDocument', 'greigeGroupNamaKain', 'greigeNamaKain', 'dateRange'], 'safe'],
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
        $query = TrnPotongGreige::find()->joinWith('stockGreige.greige.group');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['stockNoDocument'] = [
            'asc' => ['trn_stock_greige.no_document' => SORT_ASC],
            'desc' => ['trn_stock_greige.no_document' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeGroupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_potong_greige.date' => SORT_ASC],
            'desc' => ['trn_potong_greige.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_potong_greige.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_potong_greige.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_potong_greige.id' => $this->id,
            'trn_potong_greige.stock_greige_id' => $this->stock_greige_id,
            'trn_potong_greige.no_urut' => $this->no_urut,
            'trn_potong_greige.date' => $this->date,
            'trn_potong_greige.posted_at' => $this->posted_at,
            'trn_potong_greige.approved_at' => $this->approved_at,
            'trn_potong_greige.approved_by' => $this->approved_by,
            'trn_potong_greige.status' => $this->status,
            'trn_potong_greige.created_at' => $this->created_at,
            'trn_potong_greige.created_by' => $this->created_by,
            'trn_potong_greige.updated_at' => $this->updated_at,
            'trn_potong_greige.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_potong_greige.no', $this->no])
            //->andFilterWhere(['ilike', 'trn_potong_greige.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_stock_greige.no_document', $this->stockNoDocument])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
        ;

        return $dataProvider;
    }
}
