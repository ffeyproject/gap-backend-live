<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MstMesinProcessing;

/**
 * MstMesinProcessingSearch represents the model behind the search form of `common\models\ar\MstMesinProcessing`.
 */
class MstMesinProcessingSearch extends MstMesinProcessing
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nama_mesin', 'relax_mesin', 'relax_jenis_nozzle', 'relax_ukuran_nozzle', 'celup_mesin', 'celup_jenis_nozzle', 'celup_ukuran_nozzle'], 'safe'],
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
        $query = MstMesinProcessing::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['ilike', 'nama_mesin', $this->nama_mesin])
            ->andFilterWhere(['ilike', 'relax_mesin', $this->relax_mesin])
            ->andFilterWhere(['ilike', 'relax_jenis_nozzle', $this->relax_jenis_nozzle])
            ->andFilterWhere(['ilike', 'relax_ukuran_nozzle', $this->relax_ukuran_nozzle])
            ->andFilterWhere(['ilike', 'celup_mesin', $this->celup_mesin])
            ->andFilterWhere(['ilike', 'celup_jenis_nozzle', $this->celup_jenis_nozzle])
            ->andFilterWhere(['ilike', 'celup_ukuran_nozzle', $this->celup_ukuran_nozzle]);

        return $dataProvider;
    }
}
