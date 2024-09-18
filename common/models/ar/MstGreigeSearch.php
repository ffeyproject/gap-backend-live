<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MstGreigeSearch represents the model behind the search form of `common\models\ar\MstGreige`.
 */
class MstGreigeSearch extends MstGreige
{
    public $groupNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_kain', 'alias', 'no_dok_referensi'], 'safe'],
            [['gap'], 'number'],
            [['aktif'], 'boolean'],
            [['groupNamaKain'], 'safe']
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
        $query = MstGreige::find()->joinWith('group');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['groupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mst_greige.id' => $this->id,
            'mst_greige.group_id' => $this->group_id,
            'mst_greige.gap' => $this->gap,
            'mst_greige.created_at' => $this->created_at,
            'mst_greige.created_by' => $this->created_by,
            'mst_greige.updated_at' => $this->updated_at,
            'mst_greige.updated_by' => $this->updated_by,
            'mst_greige.aktif' => $this->aktif,
        ]);

        $query->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->nama_kain])
            ->andFilterWhere(['ilike', 'mst_greige.alias', $this->alias])
            ->andFilterWhere(['ilike', 'mst_greige.no_dok_referensi', $this->no_dok_referensi])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->groupNamaKain])
        ;

        return $dataProvider;
    }
}
