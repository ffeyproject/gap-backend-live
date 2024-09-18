<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnWoColor;

/**
 * TrnWoColorSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnWoColor`.
 */
class TrnWoColorSearch extends TrnWoColor
{
    public $colorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'mo_color_id', 'greige_id'], 'integer'],
            [['qty'], 'number'],
            [['note', 'colorName'], 'safe'],
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
        $query = TrnWoColor::find()->joinWith('moColor');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['colorName'] = [
            'asc' => ['trn_mo_color.color' => SORT_ASC],
            'desc' => ['trn_mo_color.color' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_wo_color.id' => $this->id,
            'trn_wo_color.sc_id' => $this->sc_id,
            'trn_wo_color.sc_greige_id' => $this->sc_greige_id,
            'trn_wo_color.mo_id' => $this->mo_id,
            'trn_wo_color.wo_id' => $this->wo_id,
            'trn_wo_color.mo_color_id' => $this->mo_color_id,
            'trn_wo_color.qty' => $this->qty,
            'trn_wo_color.greige_id' => $this->greige_id,
        ]);

        $query->andFilterWhere(['ilike', 'trn_wo_color.note', $this->note])
        ->andFilterWhere(['ilike', 'trn_mo_color.color', $this->colorName])
        ;

        return $dataProvider;
    }
}
