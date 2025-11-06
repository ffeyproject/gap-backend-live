<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnWoColor;

/**
 * TrnWoColorSearch represents the model behind the search form of `common\models\ar\TrnWoColor`.
 */
class TrnWoColorSearch extends TrnWoColor
{
    public $dateRangeSc;
    private $from_date_sc;
    private $to_date_sc;

    public $dateRangeMo;
    private $from_date_mo;
    private $to_date_mo;

    public $from_date_wo;
    public $to_date_wo;
    public $dateRangeWo;

    public $scNo;
    public $moNo;
    public $greigeName;
    public $papperTubeName;
    public $marketingName;
    public $mengetahuiName;
    public $creatorName;
    public $scGreigeNamaKain;
    public $customerName;
    public $tipeKontrak;
    public $proccess;

    public $dateRangeReadyColour;
    private $from_date_ready_colour;
    private $to_date_ready_colour;

    public $woNo;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'mo_color_id'], 'integer'],
            [['qty'], 'number'],
            [['note','dateRangeReadyColour'], 'safe'],
            [
                [
                    'scNo', 'moNo', 'woNo', 'greigeName', 'marketingName', 'mengetahuiName', 'dateRangeWo', 'dateRangeSc', 'dateRangeMo', 'creatorName',
                    'scGreigeNamaKain', 'proccess', 'papperTubeName', 'customerName', 'tipeKontrak'
                ],
                'safe'
            ]
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
        $query = TrnWoColor::find();
        $query->joinWith([
            'wo.mo.scGreige.sc.marketing as mkt',
            'wo.mo.scGreige.sc.cust',
            'greige',
            'wo.mo.scGreige.greigeGroup',
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRangeWo' => SORT_DESC,
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

        $dataProvider->sort->attributes['customerCode'] = [
            'asc' => ['mst_customer.cust_no' => SORT_ASC],
            'desc' => ['mst_customer.cust_no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['mkt.full_name' => SORT_ASC],
            'desc' => ['mkt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => ['crt.full_name' => SORT_ASC],
            'desc' => ['crt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeSc'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeMo'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeWo'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeName'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['proccess'] = [
            'asc' => ['trn_sc_greige.process' => SORT_ASC],
            'desc' => ['trn_sc_greige.process' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scGreigeNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tipeKontrak'] = [
            'asc' => ['trn_sc.tipe_kontrak' => SORT_ASC],
            'desc' => ['trn_sc.tipe_kontrak' => SORT_DESC],
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

        if(!empty($this->dateRangeWo)){
            $this->from_date_wo = substr($this->dateRangeWo, 0, 10);
            $this->to_date_wo = substr($this->dateRangeWo, 14);

            if($this->from_date_wo == $this->to_date_wo){
                $query->andFilterWhere(['trn_wo.date' => $this->from_date_wo]);
            }else{
                $query->andFilterWhere(['between', 'trn_wo.date', $this->from_date_wo, $this->to_date_wo]);
            }
        }

       if (!empty($this->dateRangeReadyColour)) {
            $this->from_date_ready_colour = strtotime(substr($this->dateRangeReadyColour, 0, 10) . ' 00:00:00');
            $this->to_date_ready_colour = strtotime(substr($this->dateRangeReadyColour, 14) . ' 23:59:59');

            // Jika hanya satu tanggal (bukan range)
            if ($this->from_date_ready_colour == $this->to_date_ready_colour) {
                $query->andFilterWhere([
                    'between',
                    'trn_wo_color.date_ready_colour',
                    $this->from_date_ready_colour,
                    $this->from_date_ready_colour + 86400 - 1, // seluruh hari itu
                ]);
            } else {
                $query->andFilterWhere([
                    'between',
                    'trn_wo_color.date_ready_colour',
                    $this->from_date_ready_colour,
                    $this->to_date_ready_colour,
                ]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'trn_wo_color.id' => $this->id,
            'trn_wo_color.sc_id' => $this->sc_id,
            'trn_wo_color.sc_greige_id' => $this->sc_greige_id,
            'trn_wo_color.mo_id' => $this->mo_id,
            'trn_wo_color.wo_id' => $this->wo_id,
            'trn_wo_color.mo_color_id' => $this->mo_color_id,
            'trn_wo_color.qty' => $this->qty,
            'trn_sc_greige.process' => $this->proccess,
            'trn_sc.tipe_kontrak' => $this->tipeKontrak,
        ]);

        $query->andFilterWhere(['ilike', 'trn_wo_color.note', $this->note])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeName])
            ->andFilterWhere(['ilike', 'crt.full_name', $this->creatorName])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->scGreigeNamaKain])
            ->andFilterWhere(['ilike', 'mst_papper_tube.name', $this->papperTubeName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
        ;

        return $dataProvider;
    }
}