<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\GudangJadiMutasi;

/**
 * GudangJadiMutasiSearch represents the model behind the search form of `common\models\ar\GudangJadiMutasi`.
 */
class GudangJadiMutasiSearch extends GudangJadiMutasi
{
    public $dateRange;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'no_urut', 'status'], 'integer'],
            [['nomor', 'date', 'pengirim', 'penerima', 'kepala_gudang', 'dept_tujuan', 'note', 'dateRange'], 'safe'],
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
        $query = GudangJadiMutasi::find();

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
            'asc' => ['created_at' => SORT_ASC],
            'desc' => ['created_at' => SORT_DESC],
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
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'nomor', $this->nomor])
            ->andFilterWhere(['ilike', 'pengirim', $this->pengirim])
            ->andFilterWhere(['ilike', 'penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'kepala_gudang', $this->kepala_gudang])
            ->andFilterWhere(['ilike', 'dept_tujuan', $this->dept_tujuan])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
