<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MutasiPfp;

/**
 * MutasiPfpSearch represents the model behind the search form of `common\models\ar\MutasiPfp`.
 */
class MutasiPfpSearch extends MutasiPfp
{
    public $dateRange;
    private $from_date;
    private $to_date;
    public $greigeGroupNamaKain;
    public $greigeNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time', 'status'], 'integer'],
            [['no_wo', 'no', 'date', 'note', 'reject_note'], 'safe'],
            [['dateRange', 'greigeGroupNamaKain', 'greigeNamaKain'], 'safe'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->status = null;
    }

    public function behaviors()
    {
        return [];
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
        $query = MutasiPfp::find();

        // add conditions that should always apply here

        $query->joinWith('greigeGroup');
        $query->joinWith('greige');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['mutasi_pfp.date' => SORT_ASC],
            'desc' => ['mutasi_pfp.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['id'] = [
            'asc' => ['mutasi_pfp.id' => SORT_ASC],
            'desc' => ['mutasi_pfp.id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeGroupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'greige_group_id' => $this->greige_group_id,
            'greige_id' => $this->greige_id,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'approval_id' => $this->approval_id,
            'approval_time' => $this->approval_time,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'no_wo', $this->no_wo])
            ->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'reject_note', $this->reject_note]);

        $query->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain]);

        if (!empty($this->dateRange)) {
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if ($this->from_date == $this->to_date) {
                $query->andFilterWhere(['mutasi_pfp.date' => $this->from_date]);
            } else {
                $query->andFilterWhere(['between', 'mutasi_pfp.date', $this->from_date, $this->to_date]);
            }
        }

        return $dataProvider;
    }
}
