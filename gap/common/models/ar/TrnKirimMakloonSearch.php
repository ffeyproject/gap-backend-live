<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKirimMakloon;

/**
 * TrnKirimMakloonSearch represents the model behind the search form of `common\models\ar\TrnKirimMakloon`.
 */
class TrnKirimMakloonSearch extends TrnKirimMakloon
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $scNo;
    public $moNo;
    public $woNo;
    public $custName;
    public $vendorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'vendor_id', 'mo_id', 'wo_id', 'no_urut', 'unit', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['date', 'no', 'note', 'penerima', 'dateRange', 'scNo', 'moNo', 'woNo', 'custName', 'vendorName'], 'safe'],
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
        $query = TrnKirimMakloon::find();
        $query->joinWith(['wo', 'mo', 'sc.cust', 'vendor']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['moNo'] = [
            'asc' => ['trn_mo.no' => SORT_ASC],
            'desc' => ['trn_mo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['custName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['vendorName'] = [
            'asc' => ['mst_vendor.name' => SORT_ASC],
            'desc' => ['mst_vendor.name' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kirim_makloon.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kirim_makloon.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kirim_makloon.id' => $this->id,
            'trn_kirim_makloon.sc_id' => $this->sc_id,
            'trn_kirim_makloon.sc_greige_id' => $this->sc_greige_id,
            'trn_kirim_makloon.vendor_id' => $this->vendor_id,
            'trn_kirim_makloon.mo_id' => $this->mo_id,
            'trn_kirim_makloon.wo_id' => $this->wo_id,
            'trn_kirim_makloon.date' => $this->date,
            'trn_kirim_makloon.no_urut' => $this->no_urut,
            'trn_kirim_makloon.unit' => $this->unit,
            'trn_kirim_makloon.status' => $this->status,
            'trn_kirim_makloon.created_at' => $this->created_at,
            'trn_kirim_makloon.created_by' => $this->created_by,
            'trn_kirim_makloon.updated_at' => $this->updated_at,
            'trn_kirim_makloon.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_makloon.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon.penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->custName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->vendorName])
        ;

        return $dataProvider;
    }
}
