<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TrnReturPengantarSearch represents the model behind the search form of `common\models\ar\TrnReturPengantar`.
 */
class TrnReturPengantarSearch extends TrnReturPengantar
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bagian', 'status'], 'integer'],
            [['date', 'no', 'penerima', 'note'], 'safe'],
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
        $query = TrnReturPengantar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
            'bagian' => $this->bagian,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
