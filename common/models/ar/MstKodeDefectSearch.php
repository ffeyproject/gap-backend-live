<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MstKodeDefectSearch represents the model behind the search form of `common\models\ar\MstKodeDefect`.
 */
class MstKodeDefectSearch extends MstKodeDefect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'no_urut'], 'integer'],
            [['kode', 'nama_defect', 'asal_defect', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // Bypass scenarios() implementation in the parent class
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
        $query = MstKodeDefect::find();

        // Add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'no_urut' => $this->no_urut,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'kode', $this->kode])
            ->andFilterWhere(['like', 'nama_defect', $this->nama_defect])
            ->andFilterWhere(['like', 'asal_defect', $this->asal_defect]);

        return $dataProvider;
    }
}