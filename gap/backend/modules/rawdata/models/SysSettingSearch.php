<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\SysSetting;

/**
 * SysSettingSearch represents the model behind the search form of `backend\modules\rawdata\models\SysSetting`.
 */
class SysSettingSearch extends SysSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_perusahaan', 'alamat', 'telp', 'fax', 'email'], 'safe'],
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
        $query = SysSetting::find();

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
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'nama_perusahaan', $this->nama_perusahaan])
            ->andFilterWhere(['ilike', 'alamat', $this->alamat])
            ->andFilterWhere(['ilike', 'telp', $this->telp])
            ->andFilterWhere(['ilike', 'fax', $this->fax])
            ->andFilterWhere(['ilike', 'email', $this->email]);

        return $dataProvider;
    }
}
