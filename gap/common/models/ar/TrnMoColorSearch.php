<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnMoColor;

/**
 * TrnMoColorSearch represents the model behind the search form of `common\models\ar\TrnMoColor`.
 */
class TrnMoColorSearch extends TrnMoColor
{
    public $marketingName;
    public $customerName;
    public $scNo;
    public $scOrientasi;
    public $woNo;
    public $moNo;
    public $dateRangeSc;
    private $from_date_sc;
    private $to_date_sc;
    public $dateRangeMo;
    private $from_date_mo;
    private $to_date_mo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id'], 'integer'],
            [['color', 'woNo', 'dateRangeSc', 'dateRangeMo', 'scNo', 'scOrientasi', 'marketingName', 'customerName', 'moNo'], 'safe'],
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
        $query = TrnMoColor::find();
        $query->joinWith(['mo.scGreige.sc.cust', 'mo.scGreige.sc.marketing']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'moNo' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRangeSc'] = [
            'asc' => ['trn_sc.date' => SORT_ASC],
            'desc' => ['trn_sc.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeMo'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scOrientasi'] = [
            'asc' => ['trn_sc.tipe_kontrak' => SORT_ASC],
            'desc' => ['trn_sc.tipe_kontrak' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['user.full_name' => SORT_ASC],
            'desc' => ['user.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['moNo'] = [
            'asc' => ['trn_mo.no' => SORT_ASC],
            'desc' => ['trn_mo.no' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRangeSc)){
            $this->from_date_sc = substr($this->dateRangeSc, 0, 10);
            $this->to_date_sc = substr($this->dateRangeSc, 14);

            if($this->from_date_sc == $this->to_date_sc){
                $query->andFilterWhere(['trn_sc.date' => $this->from_date_sc]);
            }else{
                $query->andFilterWhere(['between', 'trn_sc.date', $this->from_date_sc, $this->to_date_sc]);
            }
        }

        if(!empty($this->dateRangeMo)){
            $this->from_date_mo = substr($this->dateRangeMo, 0, 10);
            $this->to_date_mo = substr($this->dateRangeMo, 14);

            if($this->from_date_mo == $this->to_date_mo){
                $query->andFilterWhere(['trn_mo.date' => $this->from_date_mo]);
            }else{
                $query->andFilterWhere(['between', 'trn_mo.date', $this->from_date_mo, $this->to_date_mo]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_mo_color.id' => $this->id,
            'trn_mo_color.sc_id' => $this->sc_id,
            'trn_mo_color.sc_greige_id' => $this->sc_greige_id,
            'trn_mo_color.mo_id' => $this->mo_id,
            'trn_mo_color.qty' => $this->qty,
            'trn_sc.tipe_kontrak' => $this->scOrientasi,
        ]);

        $query->andFilterWhere(['ilike', 'trn_mo_color.color', $this->color])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'user.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
        ;

        return $dataProvider;
    }
}
