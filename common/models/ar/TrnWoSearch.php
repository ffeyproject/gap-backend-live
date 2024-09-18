<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnWo;

/**
 * TrnWoSearch represents the model behind the search form of `common\models\ar\TrnWo`.
 */
class TrnWoSearch extends TrnWo
{
    public $dateRangeSc;
    private $from_date_sc;
    private $to_date_sc;

    public $dateRangeMo;
    private $from_date_mo;
    private $to_date_mo;

    public $from_date;
    public $to_date;
    public $dateRange;

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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id'], 'integer'],
            [['reject_note_mengetahui', 'no', 'date', 'plastic_size', 'shipping_mark', 'note', 'note_two', 'reject_note_marketing', 'closed_note', 'batal_note'], 'safe'],
            [
                [
                    'scNo', 'moNo', 'greigeName', 'marketingName', 'mengetahuiName', 'dateRange', 'dateRangeSc', 'dateRangeMo', 'creatorName', 'scGreigeNamaKain',
                    'proccess', 'papperTubeName', 'customerName', 'tipeKontrak', 'validasi_stock'
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
        $query = TrnWo::find();
        $query->joinWith([
            'mo.scGreige.sc.marketing as mkt',
            'mo.scGreige.sc.cust',
            'greige',
            'createdBy as crt',
            'mo.scGreige.greigeGroup',
            'papperTube'
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['papperTubeName'] = [
            'asc' => ['mst_papper_tube.name' => SORT_ASC],
            'desc' => ['mst_papper_tube.name' => SORT_DESC],
        ];

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

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_wo.date' => SORT_ASC],
            'desc' => ['trn_wo.date' => SORT_DESC],
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

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_wo.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_wo.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_wo.id' => $this->id,
            'trn_wo.sc_id' => $this->sc_id,
            'trn_wo.sc_greige_id' => $this->sc_greige_id,
            'trn_wo.mo_id' => $this->mo_id,
            'trn_wo.jenis_order' => $this->jenis_order,
            'trn_wo.greige_id' => $this->greige_id,
            'trn_wo.mengetahui_id' => $this->mengetahui_id,
            'trn_wo.apv_mengetahui_at' => $this->apv_mengetahui_at,
            'trn_wo.no_urut' => $this->no_urut,
            'trn_wo.date' => $this->date,
            'trn_wo.marketing_id' => $this->marketing_id,
            'trn_wo.apv_marketing_at' => $this->apv_marketing_at,
            'trn_wo.posted_at' => $this->posted_at,
            'trn_wo.closed_at' => $this->closed_at,
            'trn_wo.closed_by' => $this->closed_by,
            'trn_wo.batal_at' => $this->batal_at,
            'trn_wo.batal_by' => $this->batal_by,
            'trn_wo.status' => $this->status,
            'trn_wo.created_at' => $this->created_at,
            'trn_wo.created_by' => $this->created_by,
            'trn_wo.updated_at' => $this->updated_at,
            'trn_wo.updated_by' => $this->updated_by,
            'handling_id' => $this->handling_id,
            'trn_sc_greige.process' => $this->proccess,
            'trn_sc.tipe_kontrak' => $this->tipeKontrak,
            'validasi_stock' => $this->validasi_stock,
        ]);

        $query->andFilterWhere(['ilike', 'trn_wo.reject_note_mengetahui', $this->reject_note_mengetahui])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_wo.plastic_size', $this->plastic_size])
            ->andFilterWhere(['ilike', 'trn_wo.shipping_mark', $this->shipping_mark])
            ->andFilterWhere(['ilike', 'trn_wo.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_wo.note_two', $this->note_two])
            ->andFilterWhere(['ilike', 'trn_wo.reject_note_marketing', $this->reject_note_marketing])
            ->andFilterWhere(['ilike', 'trn_wo.closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'trn_wo.batal_note', $this->batal_note])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeName])
            ->andFilterWhere(['ilike', 'crt.full_name', $this->creatorName])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->scGreigeNamaKain])
            ->andFilterWhere(['ilike', 'mst_papper_tube.name', $this->papperTubeName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
        ;

        return $dataProvider;
    }
}
