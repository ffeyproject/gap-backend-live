<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnWo;

/**
 * TrnWoSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnWo`.
 */
class TrnWoSearch extends TrnWo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'papper_tube_id'], 'integer'],
            [['reject_note_mengetahui', 'no', 'date', 'plastic_size', 'shipping_mark', 'note', 'note_two', 'reject_note_marketing', 'closed_note', 'batal_note'], 'safe'],
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
        $query = TrnWo::find();

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
            'sc_greige_id' => $this->sc_greige_id,
            'mo_id' => $this->mo_id,
            'jenis_order' => $this->jenis_order,
            'greige_id' => $this->greige_id,
            'mengetahui_id' => $this->mengetahui_id,
            'apv_mengetahui_at' => $this->apv_mengetahui_at,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'marketing_id' => $this->marketing_id,
            'apv_marketing_at' => $this->apv_marketing_at,
            'posted_at' => $this->posted_at,
            'closed_at' => $this->closed_at,
            'closed_by' => $this->closed_by,
            'batal_at' => $this->batal_at,
            'batal_by' => $this->batal_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'handling_id' => $this->handling_id,
            'papper_tube_id' => $this->papper_tube_id,
        ]);

        $query->andFilterWhere(['ilike', 'reject_note_mengetahui', $this->reject_note_mengetahui])
            ->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'plastic_size', $this->plastic_size])
            ->andFilterWhere(['ilike', 'shipping_mark', $this->shipping_mark])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'note_two', $this->note_two])
            ->andFilterWhere(['ilike', 'reject_note_marketing', $this->reject_note_marketing])
            ->andFilterWhere(['ilike', 'closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'batal_note', $this->batal_note]);

        return $dataProvider;
    }
}
