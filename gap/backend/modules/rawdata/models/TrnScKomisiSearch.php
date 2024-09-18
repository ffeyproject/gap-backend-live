<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnScKomisi;

/**
 * TrnScKomisiSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnScKomisi`.
 */
class TrnScKomisiSearch extends TrnScKomisi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_agen_id', 'sc_greige_id', 'tipe_komisi'], 'integer'],
            [['komisi_amount'], 'number'],
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
        $query = TrnScKomisi::find();

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
            'sc_id' => $this->sc_id,
            'sc_agen_id' => $this->sc_agen_id,
            'sc_greige_id' => $this->sc_greige_id,
            'tipe_komisi' => $this->tipe_komisi,
            'komisi_amount' => $this->komisi_amount,
        ]);

        return $dataProvider;
    }
}
