<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MstSubLocationSearch represents the model behind the search form of `common\models\ar\MstSubLocation`.
 */
class MstSubLocationSearch extends MstSubLocation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locs_loc_id'], 'integer'],
            [['locs_code', 'locs_floor_code', 'locs_line_code', 'locs_column_code', 'locs_rack_code', 'locs_description', 'locs_active'], 'safe'],
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
        $query = MstSubLocation::find()->joinWith(['location']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'locs_code' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'wms_locs_sub.locs_loc_id' => $this->locs_loc_id,
        ]);

        if ($this->locs_active !== null && $this->locs_active !== '') {
            $query->andFilterWhere(['wms_locs_sub.locs_active' => $this->locs_active]);
        }

        $query->andFilterWhere(['ilike', 'wms_locs_sub.locs_code', $this->locs_code])
            ->andFilterWhere(['ilike', 'wms_locs_sub.locs_floor_code', $this->locs_floor_code])
            ->andFilterWhere(['ilike', 'wms_locs_sub.locs_line_code', $this->locs_line_code])
            ->andFilterWhere(['ilike', 'wms_locs_sub.locs_column_code', $this->locs_column_code])
            ->andFilterWhere(['ilike', 'wms_locs_sub.locs_rack_code', $this->locs_rack_code])
            ->andFilterWhere(['ilike', 'wms_locs_sub.locs_description', $this->locs_description]);

        return $dataProvider;
    }
}
