<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\KartuProcessPrintingProcess;

/**
 * KartuProcessPrintingProcessSearch represents the model behind the search form of `backend\modules\rawdata\models\KartuProcessPrintingProcess`.
 */
class KartuProcessPrintingProcessSearch extends KartuProcessPrintingProcess
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'process_id'], 'integer'],
            [['value', 'note'], 'safe'],
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
        $query = KartuProcessPrintingProcess::find();

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
            'kartu_process_id' => $this->kartu_process_id,
            'process_id' => $this->process_id,
        ]);

        $query->andFilterWhere(['ilike', 'value', $this->value])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
