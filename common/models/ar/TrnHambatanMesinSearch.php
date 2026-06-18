<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TrnHambatanMesinSearch represents the model behind the search form of `common\models\ar\TrnHambatanMesin`.
 */
class TrnHambatanMesinSearch extends TrnHambatanMesin
{
    public $model_mesin;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['tanggal', 'created_at', 'updated_at', 'shift'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = TrnHambatanMesin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'trn_hambatan_mesin.id' => $this->id,
            'trn_hambatan_mesin.tanggal' => $this->tanggal,
        ]);

        $query->andFilterWhere(['like', 'trn_hambatan_mesin.shift', $this->shift]);

        return $dataProvider;
    }
}
