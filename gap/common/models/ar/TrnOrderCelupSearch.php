<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnOrderCelup;

/**
 * TrnOrderCelupSearch represents the model behind the search form of `common\models\ar\TrnOrderCelup`.
 */
class TrnOrderCelupSearch extends TrnOrderCelup
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $greigeGroupNamaKain;
    public $greigeNamaKain;
    public $scNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'greige_group_id', 'greige_id', 'handling_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'note', 'date', 'color', 'greigeGroupNamaKain', 'greigeNamaKain', 'scNo', 'dateRange'], 'safe'],
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
        $query = TrnOrderCelup::find()->joinWith(['greigeGroup', 'greige', 'sc']);

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

        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_order_celup.date' => SORT_ASC],
            'desc' => ['trn_order_celup.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_order_celup.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_order_celup.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_order_celup.id' => $this->id,
            'trn_order_celup.sc_id' => $this->sc_id,
            'trn_order_celup.greige_group_id' => $this->greige_group_id,
            'trn_order_celup.greige_id' => $this->greige_id,
            'trn_order_celup.handling_id' => $this->handling_id,
            'trn_order_celup.no_urut' => $this->no_urut,
            'trn_order_celup.qty' => $this->qty,
            'trn_order_celup.status' => $this->status,
            'trn_order_celup.date' => $this->date,
            'trn_order_celup.created_at' => $this->created_at,
            'trn_order_celup.created_by' => $this->created_by,
            'trn_order_celup.updated_at' => $this->updated_at,
            'trn_order_celup.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_order_celup.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_order_celup.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_order_celup.color', $this->color])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
        ;

        return $dataProvider;
    }
}
