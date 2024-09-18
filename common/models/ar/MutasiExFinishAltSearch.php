<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MutasiExFinishAlt;

/**
 * MutasiExFinishAltSearch represents the model behind the search form of `common\models\ar\MutasiExFinishAlt`.
 */
class MutasiExFinishAltSearch extends MutasiExFinishAlt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['no_referensi', 'pemohon', 'no'], 'safe'],
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
        $query = MutasiExFinishAlt::find();

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
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'no_urut' => $this->no_urut,
        ]);

        $query->andFilterWhere(['ilike', 'no_referensi', $this->no_referensi])
            ->andFilterWhere(['ilike', 'pemohon', $this->pemohon])
            ->andFilterWhere(['ilike', 'no', $this->no]);

        return $dataProvider;
    }
}
