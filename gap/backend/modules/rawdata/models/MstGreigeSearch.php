<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\MstGreige;

/**
 * MstGreigeSearch represents the model behind the search form of `backend\modules\rawdata\models\MstGreige`.
 */
class MstGreigeSearch extends MstGreige
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_kain', 'alias', 'no_dok_referensi'], 'safe'],
            [['gap', 'stock', 'booked', 'stock_pfp', 'booked_pfp', 'stock_wip', 'booked_wip', 'stock_ef', 'booked_ef'], 'number'],
            [['aktif'], 'boolean'],
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
        $query = MstGreige::find();

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
            'group_id' => $this->group_id,
            'gap' => $this->gap,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'aktif' => $this->aktif,
            'stock' => $this->stock,
            'booked' => $this->booked,
            'stock_pfp' => $this->stock_pfp,
            'booked_pfp' => $this->booked_pfp,
            'stock_wip' => $this->stock_wip,
            'booked_wip' => $this->booked_wip,
            'stock_ef' => $this->stock_ef,
            'booked_ef' => $this->booked_ef,
        ]);

        $query->andFilterWhere(['ilike', 'nama_kain', $this->nama_kain])
            ->andFilterWhere(['ilike', 'alias', $this->alias])
            ->andFilterWhere(['ilike', 'no_dok_referensi', $this->no_dok_referensi]);

        return $dataProvider;
    }
}
