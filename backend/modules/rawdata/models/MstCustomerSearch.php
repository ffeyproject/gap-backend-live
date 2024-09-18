<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\MstCustomer;

/**
 * MstCustomerSearch represents the model behind the search form of `backend\modules\rawdata\models\MstCustomer`.
 */
class MstCustomerSearch extends MstCustomer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['cust_no', 'name', 'telp', 'fax', 'email', 'address', 'cp_name', 'cp_phone', 'cp_email', 'npwp'], 'safe'],
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
        $query = MstCustomer::find();

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
            'aktif' => $this->aktif,
        ]);

        $query->andFilterWhere(['ilike', 'cust_no', $this->cust_no])
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'telp', $this->telp])
            ->andFilterWhere(['ilike', 'fax', $this->fax])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['ilike', 'cp_name', $this->cp_name])
            ->andFilterWhere(['ilike', 'cp_phone', $this->cp_phone])
            ->andFilterWhere(['ilike', 'cp_email', $this->cp_email])
            ->andFilterWhere(['ilike', 'npwp', $this->npwp]);

        return $dataProvider;
    }
}
