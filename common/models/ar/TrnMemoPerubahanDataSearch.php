<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnMemoPerubahanData;

/**
 * TrnMemoPerubahanDataSearch represents the model behind the search form of `common\models\ar\TrnMemoPerubahanData`.
 */
class TrnMemoPerubahanDataSearch extends TrnMemoPerubahanData
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $creatorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['description', 'date', 'no', 'creatorName', 'dateRange'], 'safe'],
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
        $query = TrnMemoPerubahanData::find();
        $query->joinWith(['createdBy']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => ['user.full_name' => SORT_ASC],
            'desc' => ['user.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_memo_perubahan_data.date' => SORT_ASC],
            'desc' => ['trn_memo_perubahan_data.date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_memo_perubahan_data.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_memo_perubahan_data.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_memo_perubahan_data.id' => $this->id,
            'trn_memo_perubahan_data.date' => $this->date,
            'trn_memo_perubahan_data.status' => $this->status,
            'trn_memo_perubahan_data.created_at' => $this->created_at,
            'trn_memo_perubahan_data.created_by' => $this->created_by,
            'trn_memo_perubahan_data.updated_at' => $this->updated_at,
            'trn_memo_perubahan_data.updated_by' => $this->updated_by,
            'trn_memo_perubahan_data.no_urut' => $this->no_urut,
        ]);

        $query->andFilterWhere(['ilike', 'trn_memo_perubahan_data.description', $this->description])
            ->andFilterWhere(['ilike', 'trn_memo_perubahan_data.no', $this->no])
            ->andFilterWhere(['ilike', 'user.full_name', $this->creatorName])
        ;

        return $dataProvider;
    }
}
