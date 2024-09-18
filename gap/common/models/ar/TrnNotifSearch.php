<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnNotif;

/**
 * TrnNotifSearch represents the model behind the search form of `common\models\ar\TrnNotif`.
 */
class TrnNotifSearch extends TrnNotif
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'created_at'], 'integer'],
            [['message', 'link'], 'safe'],
            [['read'], 'boolean'],
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
        $query = TrnNotif::find();

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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'read' => $this->read,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'message', $this->message])
            ->andFilterWhere(['ilike', 'link', $this->link]);

        return $dataProvider;
    }
}
