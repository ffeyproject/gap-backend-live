<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\InspectingMklBj;

/**
 * InspectingMklBjSearch represents the model behind the search form of `common\models\ar\InspectingMklBj`.
 */
class InspectingMklBjSearch extends InspectingMklBj
{
    public $greigeName;
    public $colorName;
    public $designName;
    public $articleName;

    // 🔹 tambahan untuk date range
    public $tglInspeksiRange;
    public $tglKirimRange;
    public $from_tgl_inspeksi;
    public $to_tgl_inspeksi;
    public $from_tgl_kirim;
    public $to_tgl_kirim;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'wo_color_id', 'jenis', 'satuan', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [
                [
                    'tgl_inspeksi',
                    'tgl_kirim',
                    'no_lot',
                    'no',
                    'colorName',
                    'designName',
                    'articleName',
                    'greigeName',
                    'jenis_inspek',
                    'no_memo',
                    'tglInspeksiRange',
                    'tglKirimRange'
                ],
                'safe'
            ],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InspectingMklBj::find()->joinWith(['moColor.mo', 'wo.greige']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['tgl_inspeksi' => SORT_DESC],
            ],
        ]);

        // sort tambahan
        $dataProvider->sort->attributes['designName'] = [
            'asc' => ['trn_mo.design' => SORT_ASC],
            'desc' => ['trn_mo.design' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['articleName'] = [
            'asc' => ['trn_mo.article' => SORT_ASC],
            'desc' => ['trn_mo.article' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['colorName'] = [
            'asc' => ['trn_mo_color.color' => SORT_ASC],
            'desc' => ['trn_mo_color.color' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['greigeName'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /**
         * ===============================
         * 🔹 Filter Date Range: tgl_inspeksi
         * ===============================
         */
        if (!empty($this->tglInspeksiRange)) {
            $dates = explode(' - ', $this->tglInspeksiRange);
            $this->from_tgl_inspeksi = $dates[0] ?? null;
            $this->to_tgl_inspeksi = $dates[1] ?? null;

            if ($this->from_tgl_inspeksi && $this->to_tgl_inspeksi) {
                $query->andFilterWhere(['between', 'DATE(inspecting_mkl_bj.tgl_inspeksi)', $this->from_tgl_inspeksi, $this->to_tgl_inspeksi]);
            } elseif ($this->from_tgl_inspeksi) {
                $query->andFilterWhere(['>=', 'DATE(inspecting_mkl_bj.tgl_inspeksi)', $this->from_tgl_inspeksi]);
            } elseif ($this->to_tgl_inspeksi) {
                $query->andFilterWhere(['<=', 'DATE(inspecting_mkl_bj.tgl_inspeksi)', $this->to_tgl_inspeksi]);
            }
        }

        /**
         * ===============================
         * 🔹 Filter Date Range: tgl_kirim
         * ===============================
         */
        if (!empty($this->tglKirimRange)) {
            $dates = explode(' - ', $this->tglKirimRange);
            $this->from_tgl_kirim = $dates[0] ?? null;
            $this->to_tgl_kirim = $dates[1] ?? null;

            if ($this->from_tgl_kirim && $this->to_tgl_kirim) {
                $query->andFilterWhere(['between', 'DATE(inspecting_mkl_bj.tgl_kirim)', $this->from_tgl_kirim, $this->to_tgl_kirim]);
            } elseif ($this->from_tgl_kirim) {
                $query->andFilterWhere(['>=', 'DATE(inspecting_mkl_bj.tgl_kirim)', $this->from_tgl_kirim]);
            } elseif ($this->to_tgl_kirim) {
                $query->andFilterWhere(['<=', 'DATE(inspecting_mkl_bj.tgl_kirim)', $this->to_tgl_kirim]);
            }
        }

        // filter lainnya
        $query->andFilterWhere([
            'inspecting_mkl_bj.id' => $this->id,
            'inspecting_mkl_bj.wo_id' => $this->wo_id,
            'inspecting_mkl_bj.wo_color_id' => $this->wo_color_id,
            'inspecting_mkl_bj.jenis' => $this->jenis,
            'inspecting_mkl_bj.jenis_inspek' => $this->jenis_inspek,
            'inspecting_mkl_bj.no_memo' => $this->no_memo,
            'inspecting_mkl_bj.satuan' => $this->satuan,
            'inspecting_mkl_bj.status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'inspecting_mkl_bj.no', $this->no])
            ->andFilterWhere(['ilike', 'inspecting_mkl_bj.no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'trn_mo_color.color', $this->colorName])
            ->andFilterWhere(['ilike', 'trn_mo.design', $this->designName])
            ->andFilterWhere(['ilike', 'trn_mo.article', $this->articleName])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeName]);

        return $dataProvider;
    }
}