<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\InspectingMklBj;

/**
 * InspectingMklBjSearch represents the model behind the search form of `common\models\ar\InspectingMklBj`.
 */
class InspectingMklBjSearch extends InspectingMklBj
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'wo_color_id', 'jenis', 'satuan', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'no_urut', 'delivered_at', 'delivered_by', 'inspection_table', 'jenis_inspek'], 'integer'],
            [['tgl_inspeksi', 'tgl_kirim', 'no_lot', 'no', 'delivery_reject_note', 'k3l_code', 'defect', 'no_memo', 'note'], 'safe'],
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
        $query = InspectingMklBj::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_id' => $this->wo_id,
            'wo_color_id' => $this->wo_color_id,
            'tgl_inspeksi' => $this->tgl_inspeksi,
            'tgl_kirim' => $this->tgl_kirim,
            'jenis' => $this->jenis,
            'satuan' => $this->satuan,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'no_urut' => $this->no_urut,
            'delivered_at' => $this->delivered_at,
            'delivered_by' => $this->delivered_by,
            'inspection_table' => $this->inspection_table,
            'jenis_inspek' => $this->jenis_inspek,
        ]);

        $query->andFilterWhere(['ilike', 'no_lot', $this->no_lot])
            ->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'delivery_reject_note', $this->delivery_reject_note])
            ->andFilterWhere(['ilike', 'k3l_code', $this->k3l_code])
            ->andFilterWhere(['ilike', 'defect', $this->defect])
            ->andFilterWhere(['ilike', 'no_memo', $this->no_memo])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
