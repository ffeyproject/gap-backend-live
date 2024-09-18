<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MstLocationSearch represents the model behind the search form of `common\models\ar\MstSubLocation`.
 */
class MstSubLocationSearch extends MstSubLocation
{
    public $groupNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locs_code'], 'integer'],
            [['locs_code', 'locs_description'], 'safe'],
            [['locs_active'], 'boolean']
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
        $query = MstSubLocation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        return $dataProvider;
    }
}
