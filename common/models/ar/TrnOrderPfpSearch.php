<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnOrderPfp;

/**
 * TrnOrderPfpSearch represents the model behind the search form of `common\models\ar\TrnOrderPfp`.
 */
class TrnOrderPfpSearch extends TrnOrderPfp
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $greigeGroupNamaKain;
    public $greigeNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'note', 'date', 'dateRange', 'greigeGroupNamaKain', 'greigeNamaKain'], 'safe'],
            [['qty'], 'number'],
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
        $query = TrnOrderPfp::find()->joinWith(['greigeGroup', 'greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'customerName' => SORT_ASC,
                    'dateRange' => SORT_DESC,
                    //'nomorSc' => SORT_ASC,
                    //'scId' => SORT_ASC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['greigeGroupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_order_pfp.date' => SORT_ASC],
            'desc' => ['trn_order_pfp.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_order_pfp.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_order_pfp.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_order_pfp.id' => $this->id,
            'trn_order_pfp.greige_group_id' => $this->greige_group_id,
            'trn_order_pfp.greige_id' => $this->greige_id,
            'trn_order_pfp.handling_id' => $this->handling_id,
            'trn_order_pfp.no_urut' => $this->no_urut,
            'trn_order_pfp.qty' => $this->qty,
            'trn_order_pfp.status' => $this->status,
            //'trn_order_pfp.date' => $this->date,
            'trn_order_pfp.created_at' => $this->created_at,
            'trn_order_pfp.created_by' => $this->created_by,
            'trn_order_pfp.updated_at' => $this->updated_at,
            'trn_order_pfp.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_order_pfp.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_order_pfp.note', $this->note])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
        ;

        return $dataProvider;
    }
}
