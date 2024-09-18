<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesDyeing;

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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'kartu_proses_id', 'memo_pg_at', 'memo_pg_by'], 'integer'],
            [['no', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'reject_notes', 'memo_pg', 'memo_pg_no', 'panjang', 'qty', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling', 'hasil_tes_gosok', 'motif', 'no_do', 'warna', 'tgl_order', 'buyer', 'tgl_delivery', 'nomor_kartu'], 'safe'],
            [['woNo', 'dateRange', 'motif'], 'safe'],
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
        $query->joinWith(['wo.greige']);

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

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['motif'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
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
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.nomor_kartu', $this->nomor_kartu])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->motif])
        ;

        return $dataProvider;
    }
}
