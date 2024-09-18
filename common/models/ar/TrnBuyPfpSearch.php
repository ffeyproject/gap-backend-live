<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnBuyPfp;

/**
 * TrnBuyPfpSearch represents the model behind the search form of `common\models\ar\TrnBuyPfp`.
 */
class TrnBuyPfpSearch extends TrnBuyPfp
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
            [['id', 'greige_group_id', 'greige_id', 'jenis', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'approval_id', 'approval_time'], 'integer'],
            [['no_referensi', 'no_document', 'vendor', 'note', 'date', 'reject_note', 'color', 'greigeGroupNamaKain', 'greigeNamaKain', 'dateRange'], 'safe'],
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
        $query = TrnBuyPfp::find()->joinWith(['greige', 'greigeGroup']);

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
            'asc' => ['trn_buy_pfp.date' => SORT_ASC],
            'desc' => ['trn_buy_pfp.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_buy_pfp.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_buy_pfp.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_buy_pfp.id' => $this->id,
            'trn_buy_pfp.greige_group_id' => $this->greige_group_id,
            'trn_buy_pfp.greige_id' => $this->greige_id,
            'trn_buy_pfp.status' => $this->status,
            'trn_buy_pfp.jenis' => $this->jenis,
            'trn_buy_pfp.no_referensi' => $this->no_referensi,
            'trn_buy_pfp.date' => $this->date,
            'trn_buy_pfp.created_at' => $this->created_at,
            'trn_buy_pfp.created_by' => $this->created_by,
            'trn_buy_pfp.updated_at' => $this->updated_at,
            'trn_buy_pfp.updated_by' => $this->updated_by,
            'trn_buy_pfp.approval_id' => $this->approval_id,
            'trn_buy_pfp.approval_time' => $this->approval_time,
        ]);

        $query->andFilterWhere(['ilike', 'trn_buy_pfp.no_document', $this->no_document])
            ->andFilterWhere(['ilike', 'trn_buy_pfp.vendor', $this->vendor])
            ->andFilterWhere(['ilike', 'trn_buy_pfp.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_buy_pfp.reject_note', $this->reject_note])
            ->andFilterWhere(['ilike', 'trn_buy_pfp.color', $this->color])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
        ;

        return $dataProvider;
    }
}
