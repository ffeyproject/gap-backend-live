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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'wo_color_id', 'jenis', 'satuan', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['tgl_inspeksi', 'tgl_kirim', 'no_lot', 'no', 'colorName', 'designName', 'articleName', 'greigeName', 'jenis_inspek'], 'safe'],
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
        $query = InspectingMklBj::find()->joinWith(['moColor.mo', 'wo.greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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
            'asc' => ['trn_wo.greige_id' => SORT_ASC],
            'desc' => ['trn_wo.greige_id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'inspecting_mkl_bj.id' => $this->id,
            'inspecting_mkl_bj.no' => $this->no,
            'inspecting_mkl_bj.wo_id' => $this->wo_id,
            'inspecting_mkl_bj.wo_color_id' => $this->wo_color_id,
            'inspecting_mkl_bj.tgl_inspeksi' => $this->tgl_inspeksi,
            'inspecting_mkl_bj.tgl_kirim' => $this->tgl_kirim,
            'inspecting_mkl_bj.jenis' => $this->jenis,
            'inspecting_mkl_bj.jenis_inspek' => $this->jenis_inspek,
            'inspecting_mkl_bj.satuan' => $this->satuan,
            'inspecting_mkl_bj.created_at' => $this->created_at,
            'inspecting_mkl_bj.created_by' => $this->created_by,
            'inspecting_mkl_bj.updated_at' => $this->updated_at,
            'inspecting_mkl_bj.updated_by' => $this->updated_by,
            'inspecting_mkl_bj.status' => $this->status,
            'trn_mo_color.color' => $this->colorName,
            'trn_mo.design' => $this->designName,
            'trn_mo.article' => $this->articleName,
        ]);

        $query->andFilterWhere(['ilike', 'inspecting_mkl_bj.no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeName])
        ;

        return $dataProvider;
    }
}