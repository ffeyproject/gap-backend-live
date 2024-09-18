<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\MstGreigeGroup;

/**
 * MstGreigeGroupSearch represents the model behind the search form of `backend\modules\rawdata\models\MstGreigeGroup`.
 */
class MstGreigeGroupSearch extends MstGreigeGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'jenis_kain', 'unit', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_kain', 'gramasi_kain', 'sulam_pinggir'], 'safe'],
            [['qty_per_batch', 'nilai_penyusutan'], 'number'],
            [['aktif'], 'boolean'],
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
        $query = MstGreigeGroup::find();

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
            'jenis_kain' => $this->jenis_kain,
            'qty_per_batch' => $this->qty_per_batch,
            'unit' => $this->unit,
            'nilai_penyusutan' => $this->nilai_penyusutan,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'aktif' => $this->aktif,
        ]);

        $query->andFilterWhere(['ilike', 'nama_kain', $this->nama_kain])
            ->andFilterWhere(['ilike', 'gramasi_kain', $this->gramasi_kain])
            ->andFilterWhere(['ilike', 'sulam_pinggir', $this->sulam_pinggir]);

        return $dataProvider;
    }
}
