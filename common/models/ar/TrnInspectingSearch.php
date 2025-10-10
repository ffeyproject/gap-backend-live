<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnInspecting;

class TrnInspectingSearch extends TrnInspecting
{
    public $from_date;
    public $to_date;
    public $dateRange;
    public $from_tanggal_inspeksi;
    public $to_tanggal_inspeksi;
    public $tanggalInspeksiRange;
    public $color;
    public $woNo;
    public $kpdNo;
    public $kppNo;
    public $memoRepairNo;

    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_process_dyeing_id', 'memo_repair_id', 'jenis_process', 'no_urut', 'status', 'jenis_inspek', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'unit'], 'integer'],
            [['no', 'date', 'tanggal_inspeksi', 'no_lot', 'kombinasi', 'note', 'kpdNo', 'kppNo', 'memoRepairNo', 'woNo', 'dateRange', 'tanggalInspeksiRange', 'color', 'no_memo'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TrnInspecting::find();
        $query->joinWith(['kartuProcessDyeing', 'kartuProcessPrinting', 'memoRepair', 'wo']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ]
            ],
        ]);

        // Sorting tambahan
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

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /**
         * ==============================
         * 🔹 FILTER UNTUK FIELD "date"
         * ==============================
         */
        if (!empty($this->dateRange)) {
            $dates = explode(' - ', $this->dateRange);
            $this->from_date = $dates[0] ?? null;
            $this->to_date = $dates[1] ?? null;

            if ($this->from_date && $this->to_date) {
                $query->andFilterWhere(['between', 'DATE(trn_inspecting.date)', $this->from_date, $this->to_date]);
            } elseif ($this->from_date) {
                $query->andFilterWhere(['>=', 'DATE(trn_inspecting.date)', $this->from_date]);
            } elseif ($this->to_date) {
                $query->andFilterWhere(['<=', 'DATE(trn_inspecting.date)', $this->to_date]);
            }
        }

        /**
         * =======================================
         * 🔹 FILTER UNTUK FIELD "tanggal_inspeksi"
         * =======================================
         */
        if (!empty($this->tanggalInspeksiRange)) {
            $dates = explode(' - ', $this->tanggalInspeksiRange);
            $this->from_tanggal_inspeksi = $dates[0] ?? null;
            $this->to_tanggal_inspeksi = $dates[1] ?? null;

            if ($this->from_tanggal_inspeksi && $this->to_tanggal_inspeksi) {
                $query->andFilterWhere(['between', 'DATE(trn_inspecting.tanggal_inspeksi)', $this->from_tanggal_inspeksi, $this->to_tanggal_inspeksi]);
            } elseif ($this->from_tanggal_inspeksi) {
                $query->andFilterWhere(['>=', 'DATE(trn_inspecting.tanggal_inspeksi)', $this->from_tanggal_inspeksi]);
            } elseif ($this->to_tanggal_inspeksi) {
                $query->andFilterWhere(['<=', 'DATE(trn_inspecting.tanggal_inspeksi)', $this->to_tanggal_inspeksi]);
            }
        }

        // Filter lainnya
        $query->andFilterWhere([
            'trn_inspecting.id' => $this->id,
            'trn_inspecting.sc_id' => $this->sc_id,
            'trn_inspecting.sc_greige_id' => $this->sc_greige_id,
            'trn_inspecting.mo_id' => $this->mo_id,
            'trn_inspecting.wo_id' => $this->wo_id,
            'trn_inspecting.kartu_process_dyeing_id' => $this->kartu_process_dyeing_id,
            'trn_inspecting.memo_repair_id' => $this->memo_repair_id,
            'trn_inspecting.jenis_process' => $this->jenis_process,
            'trn_inspecting.no_urut' => $this->no_urut,
            'trn_inspecting.status' => $this->status,
            'trn_inspecting.jenis_inspek' => $this->jenis_inspek,
            'trn_inspecting.no_memo' => $this->no_memo,
            'trn_inspecting.unit' => $this->unit,
        ]);

        $query->andFilterWhere(['ilike', 'trn_inspecting.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_inspecting.no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'trn_inspecting.kombinasi', $this->kombinasi])
            ->andFilterWhere(['ilike', 'trn_inspecting.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_dyeing.no', $this->kpdNo])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.no', $this->kppNo])
            ->andFilterWhere(['ilike', 'trn_memo_repaair.no', $this->memoRepairNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo]);

        return $dataProvider;
    }
}