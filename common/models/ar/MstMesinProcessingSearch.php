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
            [['nama_mesin', 'jenis_mesin', 'jenis_nozzle', 'ukuran_nozzle'], 'safe'],
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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['ilike', 'nama_mesin', $this->nama_mesin])
            ->andFilterWhere(['ilike', 'jenis_mesin', $this->jenis_mesin])
            ->andFilterWhere(['ilike', 'jenis_nozzle', $this->jenis_nozzle])
            ->andFilterWhere(['ilike', 'ukuran_nozzle', $this->ukuran_nozzle]);

        return $dataProvider;
    }
}
