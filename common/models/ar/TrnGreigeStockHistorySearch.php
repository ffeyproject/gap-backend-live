<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGreigeStockHistory;

/**
 * TrnGreigeStockHistorySearch represents the model behind the search form of `common\models\ar\TrnGreigeStockHistory`.
 */
class TrnGreigeStockHistorySearch extends TrnGreigeStockHistory
{
    public $dateRange;
    public $greigeName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_id', 'created_by'], 'integer'],
            [
                [
                    'gap_old', 'gap_new',
                    'stock_old', 'stock_new',
                    'available_old', 'available_new',
                    'booked_wo_old', 'booked_wo_new',
                    'stock_pfp_old', 'stock_pfp_new',
                    'stock_wip_old', 'stock_wip_new',
                    'stock_ef_old', 'stock_ef_new',
                    'booked_old', 'booked_new',
                    'booked_pfp_old', 'booked_pfp_new',
                    'booked_wip_old', 'booked_wip_new',
                    'booked_ef_old', 'booked_ef_new',
                    'booked_opfp_old', 'booked_opfp_new',
                    'available_pfp_old', 'available_pfp_new',
                    'stock_opname_old', 'stock_opname_new'
                ], 'number'
            ],
            [['created_at', 'context', 'dateRange', 'greigeName'], 'safe'],
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
        $query = TrnGreigeStockHistory::find();
        $query->joinWith(['greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['greigeName'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_greige_stock_history.id' => $this->id,
            'trn_greige_stock_history.greige_id' => $this->greige_id,
            'trn_greige_stock_history.created_by' => $this->created_by,
        ]);

        if (!empty($this->dateRange)) {
            $from_date = substr($this->dateRange, 0, 10) . ' 00:00:00';
            $to_date = substr($this->dateRange, 14, 10) . ' 23:59:59';
            $query->andWhere(['between', 'trn_greige_stock_history.created_at', $from_date, $to_date]);
        }

        $query->andFilterWhere(['ilike', 'trn_greige_stock_history.context', $this->context])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeName]);

        return $dataProvider;
    }
}
