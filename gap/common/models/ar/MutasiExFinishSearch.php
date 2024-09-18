<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MutasiExFinish;

/**
 * MutasiExFinishSearch represents the model behind the search form of `common\models\ar\MutasiExFinish`.
 */
class MutasiExFinishSearch extends MutasiExFinish
{
    public $greigeGroupNamaKain;
    public $greigeNamaKain;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_group_id', 'greige_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['no_wo', 'no', 'date', 'note', 'greigeGroupNamaKain', 'greigeNamaKain', 'dateRange'], 'safe'],
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
        $query = MutasiExFinish::find()->joinWith(['greige', 'greigeGroup']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeGroupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['mutasi_ex_finish.date' => SORT_ASC],
            'desc' => ['mutasi_ex_finish.date' => SORT_DESC],
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
                $query->andFilterWhere(['mutasi_ex_finish.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'mutasi_ex_finish.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mutasi_ex_finish.id' => $this->id,
            'mutasi_ex_finish.greige_group_id' => $this->greige_group_id,
            'mutasi_ex_finish.greige_id' => $this->greige_id,
            'mutasi_ex_finish.no_urut' => $this->no_urut,
            'mutasi_ex_finish.date' => $this->date,
            'mutasi_ex_finish.created_at' => $this->created_at,
            'mutasi_ex_finish.created_by' => $this->created_by,
            'mutasi_ex_finish.updated_at' => $this->updated_at,
            'mutasi_ex_finish.updated_by' => $this->updated_by,
            'mutasi_ex_finish.status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'mutasi_ex_finish.no_wo', $this->no_wo])
            ->andFilterWhere(['ilike', 'mutasi_ex_finish.no', $this->no])
            ->andFilterWhere(['ilike', 'mutasi_ex_finish.note', $this->note])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
        ;

        return $dataProvider;
    }
}
