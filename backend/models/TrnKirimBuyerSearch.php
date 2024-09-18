<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKirimBuyer;

/**
 * TrnKirimBuyerSearch represents the model behind the search form of `common\models\ar\TrnKirimBuyer`.
 */
class TrnKirimBuyerSearch extends TrnKirimBuyer
{
    public $marketingName;
    public $customerName;
    public $scNo;
    public $scDate;
    public $scOrientasi;
    public $scNoPo;
    public $scCurrencyId;
    public $scGreigeNamaKain;
    public $scGreigeProcessId;
    public $scGreigeGrade;
    public $moNo;
    public $headerStatus;
    public $headerNo;
    public $dateRange;
    private $from_date;
    private $to_date;

    public $woNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id','unit', 'header_id'], 'integer'],
            [['nama_kain_alias', 'note', 'scNo', 'moNo', 'woNo', 'custName'], 'safe'],
            [
                [
                    'woNo', 'dateRange', 'scNo', 'scOrientasi', 'scDate', 'scNoPo', 'scCurrencyId', 'marketingName', 'customerName', 'color',
                    'scGreigeNamaKain', 'scGreigeProcessId', 'scGreigeGrade', 'moNo', 'headerStatus', 'headerNo'
                ],
                'safe'
            ],
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
        $query = TrnKirimBuyer::find();
        $query->joinWith(['wo.mo.scGreige.sc.cust', 'wo.mo.scGreige.sc.marketing', 'wo.mo.scGreige.greigeGroup', 'header']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_kirim_buyer_header.date' => SORT_ASC],
            'desc' => ['trn_kirim_buyer_header.date' => SORT_DESC],
        ];

        /*$dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
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
        ];*/

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
            'trn_kirim_buyer.id' => $this->id,
            'trn_kirim_buyer.sc_id' => $this->sc_id,
            'trn_kirim_buyer.sc_greige_id' => $this->sc_greige_id,
            'trn_kirim_buyer.mo_id' => $this->mo_id,
            'trn_kirim_buyer.wo_id' => $this->wo_id,
            'trn_kirim_buyer.unit' => $this->unit,
            'trn_kirim_buyer.header_id' => $this->header_id,
            'trn_sc.tipe_kontrak' => $this->scOrientasi,
            'trn_sc.currency' => $this->scCurrencyId,
            'trn_sc_greige.process' => $this->scGreigeProcessId,
            'trn_sc_greige.grade' => $this->scGreigeGrade,
            'trn_kirim_buyer_header.status' => $this->headerStatus,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_buyer.nama_kain_alias', $this->nama_kain_alias])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'user.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_sc.no_po', $this->scNoPo])
            ->andFilterWhere(['ilike', 'trn_sc.date', $this->scDate])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->scGreigeNamaKain])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer_header.no', $this->headerNo])
        ;

        return $dataProvider;
    }
}
