<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MstHandling;

/**
 * MstHandlingSearch represents the model behind the search form of `common\models\ar\MstHandling`.
 */
class MstHandlingSearch extends MstHandling
{
    public $greigeNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'lebar_preset', 'lebar_finish', 'berat_finish', 'densiti_lusi', 'densiti_pakan', 'no_hanger', 'greigeNamaKain'], 'safe'],
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
        $query = MstHandling::find();
        $query->joinWith('greige');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mst_handling.id' => $this->id,
            'mst_handling.greige_id' => $this->greige_id,
            'mst_handling.created_at' => $this->created_at,
            'mst_handling.created_by' => $this->created_by,
            'mst_handling.updated_at' => $this->updated_at,
            'mst_handling.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'mst_handling.name', $this->name])
            ->andFilterWhere(['ilike', 'mst_handling.lebar_preset', $this->lebar_preset])
            ->andFilterWhere(['ilike', 'mst_handling.lebar_finish', $this->lebar_finish])
            ->andFilterWhere(['ilike', 'mst_handling.berat_finish', $this->berat_finish])
            ->andFilterWhere(['ilike', 'mst_handling.densiti_lusi', $this->densiti_lusi])
            ->andFilterWhere(['ilike', 'mst_handling.densiti_pakan', $this->densiti_pakan])
            ->andFilterWhere(['ilike', 'mst_handling.no_hanger', $this->no_hanger])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
        ;

        return $dataProvider;
    }
}
