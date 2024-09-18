<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\SuratJalanExFinish;

/**
 * SuratJalanExFinishSearch represents the model behind the search form of `common\models\ar\SuratJalanExFinish`.
 */
class SuratJalanExFinishSearch extends SuratJalanExFinish
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'memo_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'pengirim', 'penerima', 'kepala_gudang', 'note'], 'safe'],
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
        $query = SuratJalanExFinish::find();

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
            'memo_id' => $this->memo_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'pengirim', $this->pengirim])
            ->andFilterWhere(['ilike', 'penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'kepala_gudang', $this->kepala_gudang])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
