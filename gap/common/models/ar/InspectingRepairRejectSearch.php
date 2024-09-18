<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\InspectingRepairReject;

/**
 * InspectingRepairRejectSearch represents the model behind the search form of `common\models\ar\InspectingRepairReject`.
 */
class InspectingRepairRejectSearch extends InspectingRepairReject
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'memo_repair_id', 'no_urut', 'created_at', 'created_by'], 'integer'],
            [['no', 'date', 'untuk_bagian', 'pcs', 'keterangan', 'penerima', 'mengetahui', 'pengirim'], 'safe'],
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
        $query = InspectingRepairReject::find();

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
            'memo_repair_id' => $this->memo_repair_id,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'untuk_bagian', $this->untuk_bagian])
            ->andFilterWhere(['ilike', 'pcs', $this->pcs])
            ->andFilterWhere(['ilike', 'keterangan', $this->keterangan])
            ->andFilterWhere(['ilike', 'penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'mengetahui', $this->mengetahui])
            ->andFilterWhere(['ilike', 'pengirim', $this->pengirim]);

        return $dataProvider;
    }
}
