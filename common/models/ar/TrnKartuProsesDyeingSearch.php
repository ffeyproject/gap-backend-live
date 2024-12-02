<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesDyeing;
use yii\db\Expression;

/**
 * TrnKartuProsesDyeingSearch represents the model behind the search form of `common\models\ar\TrnKartuProsesDyeing`.
 */
class TrnKartuProsesDyeingSearch extends TrnKartuProsesDyeing
{
    public $woNo;
    public $dateRange;
    private $from_date;
    private $to_date;
    public $motif;
    public $woDateRange;
    public $openDateRange;
    public $marketingName;
    public $dateRangeMasukPacking;
    public $customerName;
    public $warna;
    public $ready_colour;
    public $dateRangeReadyColour;
    public $dateReangeTopingMatching;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'kartu_proses_id', 'memo_pg_at', 'memo_pg_by'], 'integer'],
            [['no', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'reject_notes', 'memo_pg', 'memo_pg_no', 'panjang', 'qty', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling', 'hasil_tes_gosok', 'motif', 'no_do', 'warna', 'tgl_order', 'buyer', 'tgl_delivery', 'nomor_kartu'], 'safe'],
            [['woNo', 'dateRange', 'motif','woDateRange','openDateRange','marketingName', 'dateRangeMasukPacking','customerName','dateRangeReadyColour','dateReangeTopingMatching','status'], 'safe'],
            [['toping_matching','ready_colour'], 'boolean'],
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
        $query = TrnKartuProsesDyeing::find();
        // $query->joinWith(['wo.greige']);
        $query->joinWith(['wo.greige', 'kartuProcessDyeingProcesses kpd' => function($query) {
            $query->onCondition(['kpd.process_id' => 1]);
        }]);    
        $query->joinWith(['mo.scGreige.sc.marketing as mkt']);
        $query->joinWith(['sc.cust as cust']);
        $query->joinWith(['woColor.moColor as moColor']);



        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_kartu_proses_dyeing.date' => SORT_ASC],
            'desc' => ['trn_kartu_proses_dyeing.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woDateRange'] = [
            'asc' => ['trn_wo.date' => SORT_ASC],
            'desc' => ['trn_wo.date' => SORT_DESC],
        ];


        $dataProvider->sort->attributes['dateRangeMasukPacking'] = [
            'asc' => ['trn_kartu_proses_dyeing.approved_at' => SORT_ASC],
            'desc' => ['trn_kartu_proses_dyeing.approved_at' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['openDateRange'] = [
            'asc' => [new Expression("CAST(kpd.value AS jsonb)->>'tanggal' ASC")],
            'desc' => [new Expression("CAST(kpd.value AS jsonb)->>'tanggal' DESC")],
        ];

        $dataProvider->sort->attributes['motif'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['mkt.full_name' => SORT_ASC],
            'desc' => ['mkt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['cust.name' => SORT_ASC],
            'desc' => ['cust.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['warna'] = [
            'asc' => ['moColor.color' => SORT_ASC],
            'desc' => ['cusmoColort.color' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kartu_proses_dyeing.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kartu_proses_dyeing.date', $this->from_date, $this->to_date]);
            }
        }

        if(!empty($this->woDateRange)){
            $this->from_date = substr($this->woDateRange, 0, 10);
            $this->to_date = substr($this->woDateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_wo.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_wo.date', $this->from_date, $this->to_date]);
            }
        }

        if(!empty($this->openDateRange)){
            $this->from_date = substr($this->openDateRange, 0, 10);
            $this->to_date = substr($this->openDateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andWhere(new Expression("CAST(kpd.value AS jsonb)->>'tanggal' = :from_date"))
                ->addParams([':from_date' => $this->from_date]);
            }else{
                $query->andFilterWhere([
                    'between', 
                    new Expression("CAST(kpd.value AS jsonb)->>'tanggal'"), 
                    $this->from_date, 
                    $this->to_date
                ]);
            }
        }

        if (!empty($this->dateRangeMasukPacking)) {
            // Explode the date range into start and end dates
            list($start_date, $end_date) = explode(' to ', $this->dateRangeMasukPacking);
    
            // Convert the dates to UNIX timestamp
            $start_timestamp = strtotime($start_date);
            $end_timestamp = strtotime($end_date . ' 23:59:59'); // end of the day
    
            // Apply the filter between the two timestamps
            $query->andFilterWhere(['between', 'trn_kartu_proses_dyeing.approved_at', $start_timestamp, $end_timestamp]);
        }

        if ($this->ready_colour !== null) {
            $query->andFilterWhere(['trn_wo_color.ready_colour' => $this->ready_colour]);
        }

        if (!empty($this->dateRangeReadyColour)) {
            // Explode the date range into start and end dates
            list($start_date, $end_date) = explode(' to ', $this->dateRangeReadyColour);
    
            // Convert the dates to UNIX timestamp
            $start_timestamp = strtotime($start_date);
            $end_timestamp = strtotime($end_date . ' 23:59:59'); // end of the day
    
            // Apply the filter between the two timestamps
            $query->andFilterWhere(['between', 'trn_wo_color.date_ready_colour', $start_timestamp, $end_timestamp]);
        }


        if (!empty($this->dateReangeTopingMatching)) {
            // Explode the date range into start and end dates
            list($start_date, $end_date) = explode(' to ', $this->dateReangeTopingMatching);
    
            // Convert the dates to UNIX timestamp
            $start_timestamp = strtotime($start_date);
            $end_timestamp = strtotime($end_date . ' 23:59:59'); // end of the day
    
            // Apply the filter between the two timestamps
            $query->andFilterWhere(['between', 'trn_kartu_proses_dyeing.date_toping_matching', $start_timestamp, $end_timestamp]);
        }
        
        $isFiltering = false;
        if (!empty($this->openDateRange)) {
            $isFiltering = true;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kartu_proses_dyeing.id' => $this->id,
            'trn_kartu_proses_dyeing.sc_id' => $this->sc_id,
            'trn_kartu_proses_dyeing.sc_greige_id' => $this->sc_greige_id,
            'trn_kartu_proses_dyeing.mo_id' => $this->mo_id,
            'trn_kartu_proses_dyeing.wo_id' => $this->wo_id,
            'trn_kartu_proses_dyeing.no_urut' => $this->no_urut,
            'trn_kartu_proses_dyeing.asal_greige' => $this->asal_greige,
            'trn_kartu_proses_dyeing.date' => $this->date,
            'trn_kartu_proses_dyeing.posted_at' => $this->posted_at,
            'trn_kartu_proses_dyeing.approved_at' => $this->approved_at,
            'trn_kartu_proses_dyeing.approved_by' => $this->approved_by,
            'trn_kartu_proses_dyeing.delivered_at' => $this->delivered_at,
            'trn_kartu_proses_dyeing.delivered_by' => $this->delivered_by,
            'trn_kartu_proses_dyeing.status' => $this->status,
            'trn_kartu_proses_dyeing.created_at' => $this->created_at,
            'trn_kartu_proses_dyeing.created_by' => $this->created_by,
            'trn_kartu_proses_dyeing.updated_at' => $this->updated_at,
            'trn_kartu_proses_dyeing.updated_by' => $this->updated_by,
            'trn_kartu_proses_dyeing.toping_matching' => $this->toping_matching,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.nomor_kartu', $this->nomor_kartu])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->motif])
            ->andFilterWhere(['ilike', 'cust.name', $this->customerName])
            ->andFilterWhere(['ilike', 'moColor.color', $this->warna])
        ;

        if ($isFiltering) {
            $query->andWhere(['IS NOT', 'kpd.value', null])
            ->orderBy([new Expression("CAST(kpd.value AS jsonb)->>'tanggal' ASC")]);
        }

        $this->to_date = null;
        $this->from_date = null;

        return $dataProvider;
    }
}
