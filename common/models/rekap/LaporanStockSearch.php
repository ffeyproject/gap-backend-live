<?php

namespace common\models\rekap;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnStockGreige;

/**
 * TrnStockGreigeSearch represents the model behind the search form of `common\models\ar\TrnStockGreige`.
 */
class LaporanStockSearch extends TrnStockGreige
{
    public $greigeNamaKain;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //['date', 'required'],
            ['dateRange', 'required'],
            [['greigeNamaKain'], 'string'],
            [['greige_id', 'lot_lusi', 'lot_pakan', 'status_tsd','asal_greige'], 'safe'],
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
        $query = TrnStockGreige::find()->joinWith(['greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_stock_greige.date' => SORT_ASC],
            'desc' => ['trn_stock_greige.date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $this->from_date = substr($this->dateRange, 0, 10);
        $this->to_date = substr($this->dateRange, 14);

        if($this->from_date == $this->to_date){
            $query->andFilterWhere(['trn_stock_greige.date' => $this->from_date]);
        }else{
            $query->andFilterWhere(['between', 'trn_stock_greige.date', $this->from_date, $this->to_date]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_stock_greige.greige_id' => $this->greige_id,
            'trn_stock_greige.status_tsd' => $this->status_tsd,
            'trn_stock_greige.asal_greige' => $this->asal_greige,
            //'trn_stock_greige.date' => $this->date,
        ]);

        $query->andFilterWhere(['ilike', 'trn_stock_greige.lot_lusi', $this->lot_lusi])
            ->andFilterWhere(['ilike', 'trn_stock_greige.lot_pakan', $this->lot_pakan])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
        ;

        return $dataProvider;
    }
}
