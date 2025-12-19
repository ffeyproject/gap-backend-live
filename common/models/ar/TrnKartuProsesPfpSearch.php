<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\MstProcessPfp;
use yii\db\Expression;

class TrnKartuProsesPfpSearch extends TrnKartuProsesPfp
{
    public $orderPfpNo;
    public $dateRange;
    public $namaKain;
    public $shift;

    private $from_date;
    private $to_date;

    public function rules()
    {
        return [
            [['id','greige_group_id','greige_id','order_pfp_id','no_urut','asal_greige',
              'posted_at','approved_at','approved_by','status','created_at','created_by',
              'updated_at','updated_by','delivered_at','delivered_by'], 'integer'],

            [['no','no_proses','dikerjakan_oleh','lusi','pakan','note','date',
              'reject_notes','nomor_kartu','namaKain','orderPfpNo','dateRange','shift'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        /** ambil process awal PFP (order = 1) */
        $processId = MstProcessPfp::find()
            ->select('id')
            ->where(['order' => 1])
            ->scalar();

        $query = TrnKartuProsesPfp::find();

        /** JOIN */
        $query->joinWith(['orderPfp']);
        $query->joinWith(['greige']);
        $query->joinWith(['kartuProcessPfpProcesses kp']);

        $query->select([
            'trn_kartu_proses_pfp.*',
            new Expression("kp.value::jsonb->>'shift_operator' AS shift"),
        ]);

        /** FILTER hanya process awal */
        if ($processId) {
            $query->andWhere(['kp.process_id' => $processId]);
        }

        /** GROUP BY wajib (PostgreSQL) */
        $query->groupBy([
            'trn_kartu_proses_pfp.id',
            'kp.value'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                /** DEFAULT SORT: SHIFT ASC (A → Z) */
                'defaultOrder' => [
                    'shift' => SORT_ASC,
                ],
            ],
        ]);

        /** SORTING */
        $dataProvider->sort->attributes['shift'] = [
            'asc'  => ['shift' => SORT_ASC],
            'desc' => ['shift' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['namaKain'] = [
            'asc'  => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc'  => ['trn_kartu_proses_pfp.date' => SORT_ASC],
            'desc' => ['trn_kartu_proses_pfp.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderPfpNo'] = [
            'asc'  => ['trn_order_pfp.no' => SORT_ASC],
            'desc' => ['trn_order_pfp.no' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** DATE RANGE FILTER */
        if (!empty($this->dateRange)) {
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date   = substr($this->dateRange, 14);

            if ($this->from_date === $this->to_date) {
                $query->andWhere(['trn_kartu_proses_pfp.date' => $this->from_date]);
            } else {
                $query->andWhere([
                    'between',
                    'trn_kartu_proses_pfp.date',
                    $this->from_date,
                    $this->to_date
                ]);
            }
        }

        /** FILTER UTAMA */
        $query->andFilterWhere([
            'trn_kartu_proses_pfp.id' => $this->id,
            'trn_kartu_proses_pfp.greige_group_id' => $this->greige_group_id,
            'trn_kartu_proses_pfp.greige_id' => $this->greige_id,
            'trn_kartu_proses_pfp.order_pfp_id' => $this->order_pfp_id,
            'trn_kartu_proses_pfp.no_urut' => $this->no_urut,
            'trn_kartu_proses_pfp.asal_greige' => $this->asal_greige,
            'trn_kartu_proses_pfp.status' => $this->status,
        ]);

        /** FILTER STRING */
        $query->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_pfp.nomor_kartu', $this->nomor_kartu])
            ->andFilterWhere(['ilike', 'trn_order_pfp.no', $this->orderPfpNo])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->namaKain])
            ->andFilterWhere(['ilike', 'shift', $this->shift]);

        return $dataProvider;
    }
}