<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGreigeKeluar;

/**
 * TrnGreigeKeluarSearch represents the model behind the search form of `common\models\ar\TrnGreigeKeluar`.
 */
class TrnGreigeKeluarSearch extends TrnGreigeKeluar
{
    public $dateRange;
    public $woNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'no_urut', 'jenis', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date', 'note', 'destinasi', 'dateRange','woNo'], 'safe'],
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
        $query->joinWith(['wo']);

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

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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
            'id' => $this->id,
            'no_urut' => $this->no_urut,
            'jenis' => $this->jenis,
            'date' => $this->date,
            'wo_id' => $this->wo_id,
            'posted_at' => $this->posted_at,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'destinasi', $this->destinasi])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
        ;

        return $dataProvider;
    }
}
