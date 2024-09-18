<?php

namespace backend\models\search;


use common\models\ar\TrnGreigeKeluar;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LaporanGreigeKeluar extends TrnGreigeKeluar
{
    public $dateRange;
    public $greigeId;
    public $jenis;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['greigeId', 'required'],
            [['jenis', 'greigeId'], 'integer'],
            [['dateRange'], 'safe'],
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
        $query = TrnGreigeKeluar::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['date' => SORT_ASC],
            'desc' => ['date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $from_date = substr($this->dateRange, 0, 10);
            $to_date = substr($this->dateRange, 14);

            if($from_date == $to_date){
                $query->andWhere(['date' => $from_date]);
            }else{
                $query->andWhere(['between', 'date', $from_date, $to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'jenis' => $this->jenis,
        ]);

        return $dataProvider;
    }
}