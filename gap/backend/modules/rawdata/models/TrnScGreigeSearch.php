<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnScGreige;

/**
 * TrnScGreigeSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnScGreige`.
 */
class TrnScGreigeSearch extends TrnScGreige
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'greige_group_id', 'process', 'lebar_kain', 'grade', 'no_urut_order_greige'], 'integer'],
            [['merek', 'piece_length', 'price_param', 'woven_selvedge', 'note', 'closing_note', 'no_order_greige', 'order_greige_note'], 'safe'],
            [['unit_price', 'qty'], 'number'],
            [['closed'], 'boolean'],
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
        $query = TrnScGreige::find();

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
            'greige_group_id' => $this->greige_group_id,
            'process' => $this->process,
            'lebar_kain' => $this->lebar_kain,
            'grade' => $this->grade,
            'unit_price' => $this->unit_price,
            'qty' => $this->qty,
            'closed' => $this->closed,
            'no_urut_order_greige' => $this->no_urut_order_greige,
        ]);

        $query->andFilterWhere(['ilike', 'merek', $this->merek])
            ->andFilterWhere(['ilike', 'piece_length', $this->piece_length])
            ->andFilterWhere(['ilike', 'price_param', $this->price_param])
            ->andFilterWhere(['ilike', 'woven_selvedge', $this->woven_selvedge])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'closing_note', $this->closing_note])
            ->andFilterWhere(['ilike', 'no_order_greige', $this->no_order_greige])
            ->andFilterWhere(['ilike', 'order_greige_note', $this->order_greige_note]);

        return $dataProvider;
    }
}
