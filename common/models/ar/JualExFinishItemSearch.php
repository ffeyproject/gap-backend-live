<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\JualExFinishItem;

/**
 * JualExFinishItemSearch represents the model behind the search form of `common\models\ar\JualExFinishItem`.
 */
class JualExFinishItemSearch extends JualExFinishItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'jual_id', 'greige_id', 'grade', 'unit'], 'integer'],
            [['qty'], 'number'],
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
        $query = JualExFinishItem::find();

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
            'jual_id' => $this->jual_id,
            'greige_id' => $this->greige_id,
            'grade' => $this->grade,
            'qty' => $this->qty,
            'unit' => $this->unit,
        ]);

        return $dataProvider;
    }
}
