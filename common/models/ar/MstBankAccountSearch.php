<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MstBankAccount;

/**
 * MstBankAccountSearch represents the model behind the search form of `common\models\ar\MstBankAccount`.
 */
class MstBankAccountSearch extends MstBankAccount
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['bank_name', 'acct_no', 'acct_name', 'swift_code', 'address', 'correspondence'], 'safe'],
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
        $query = MstBankAccount::find();

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

        $query->andFilterWhere(['ilike', 'bank_name', $this->bank_name])
            ->andFilterWhere(['ilike', 'acct_no', $this->acct_no])
            ->andFilterWhere(['ilike', 'acct_name', $this->acct_name])
            ->andFilterWhere(['ilike', 'swift_code', $this->swift_code])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['ilike', 'correspondence', $this->correspondence]);

        return $dataProvider;
    }
}
