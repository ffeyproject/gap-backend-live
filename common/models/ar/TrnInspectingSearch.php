<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnInspecting;

/**
 * TrnInspectingSearch represents the model behind the search form of `common\models\ar\TrnInspecting`.
 */
class TrnInspectingSearch extends TrnInspecting
{
    public $from_date;
    public $to_date;
    public $dateRange;
    public $color;

    public $woNo;
    public $kpdNo;
    public $kppNo;
    public $memoRepairNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_dyeing_id', 'memo_repair_id', 'jenis_process', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by','unit'], 'integer'],
            [['no', 'date', 'tanggal_inspeksi', 'no_lot', 'kombinasi', 'note', 'kpdNo', 'kppNo', 'memoRepairNo', 'woNo', 'dateRange', 'color'], 'safe'],
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
        $query = TrnInspecting::find();
        $query->joinWith(['kartuProcessDyeing', 'kartuProcessPrinting', 'memoRepair', 'wo']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['kpdNo'] = [
            'asc' => ['trn_kartu_proses_dyeing.no' => SORT_ASC],
            'desc' => ['trn_kartu_proses_dyeing.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['kppNo'] = [
            'asc' => ['trn_kartu_proses_printing.no' => SORT_ASC],
            'desc' => ['trn_kartu_proses_printing.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['memoRepairNo'] = [
            'asc' => ['trn_memo_repaair.no' => SORT_ASC],
            'desc' => ['trn_memo_repaair.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_inspecting.date' => SORT_ASC],
            'desc' => ['trn_inspecting.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['unit'] = [
            'asc' => ['trn_inspecting.unit' => SORT_ASC],
            'desc' => ['trn_inspecting.unit' => SORT_DESC],
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
                $query->andWhere(['trn_inspecting.date' => $this->from_date]);
            }else{
                $query->andWhere(['between', 'trn_inspecting.date', $this->from_date, $this->to_date]);
            }
        }

        if(!empty($this->color)){

        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_inspecting.id' => $this->id,
            'trn_inspecting.sc_id' => $this->sc_id,
            'trn_inspecting.sc_greige_id' => $this->sc_greige_id,
            'trn_inspecting.mo_id' => $this->mo_id,
            'trn_inspecting.wo_id' => $this->wo_id,
            'trn_inspecting.kartu_process_dyeing_id' => $this->kartu_process_dyeing_id,
            'trn_inspecting.memo_repair_id' => $this->memo_repair_id,
            'trn_inspecting.jenis_process' =>$this->jenis_process,
            'trn_inspecting.no_urut' => $this->no_urut,
            'trn_inspecting.date' => $this->date,
            'trn_inspecting.tanggal_inspeksi' => $this->tanggal_inspeksi,
            'trn_inspecting.status' => $this->status,
            'trn_inspecting.created_at' => $this->created_at,
            'trn_inspecting.created_by' => $this->created_by,
            'trn_inspecting.updated_at' => $this->updated_at,
            'trn_inspecting.updated_by' => $this->updated_by,
            'trn_inspecting.approved_at' => $this->approved_at,
            'trn_inspecting.approved_by' => $this->approved_by,
            'trn_inspecting.delivered_at' => $this->delivered_at,
            'trn_inspecting.delivered_by' => $this->delivered_by,
            'trn_inspecting.unit' => $this->unit,
        ]);

        $query->andFilterWhere(['ilike', 'trn_inspecting.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_inspecting.no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'trn_inspecting.kombinasi', $this->kombinasi])
            ->andFilterWhere(['ilike', 'trn_inspecting.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.no', $this->kpdNo])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.no', $this->kppNo])
            ->andFilterWhere(['ilike', 'trn_memo_repaair.no', $this->memoRepairNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
        ;

        return $dataProvider;
    }
}
