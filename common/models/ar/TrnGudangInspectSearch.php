<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGudangInspect;

/**
 * TrnStockGreigeSearch represents the model behind the search form of `common\models\ar\TrnStockGreige`.
 */
class TrnGudangInspectSearch extends TrnGudangInspect
{
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
            [['id', 'greige_group_id', 'greige_id', 'asal_greige', 'status_tsd', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'jenis_gudang', 'keputusan_qc', 'pfp_jenis_gudang'], 'integer'],
            [['is_hasil_mix', 'is_pemotongan'], 'boolean'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_document', 'operator', 'mengetahui', 'note', 'date', 'nomor_wo', 'color', 'greigeNamaKain', 'dateRange'], 'safe'],
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
        $query = TrnGudangInspect::find()->joinWith(['greige']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_gudang_inspect.date' => SORT_ASC],
            'desc' => ['trn_gudang_inspect.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_gudang_inspect.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_gudang_inspect.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_gudang_inspect.id' => $this->id,
            'trn_gudang_inspect.greige_group_id' => $this->greige_group_id,
            'trn_gudang_inspect.greige_id' => $this->greige_id,
            'trn_gudang_inspect.asal_greige' => $this->asal_greige,
            'trn_gudang_inspect.status_tsd' => $this->status_tsd,
            'trn_gudang_inspect.status' => $this->status,
            'trn_gudang_inspect.date' => $this->date,
            'trn_gudang_inspect.created_at' => $this->created_at,
            'trn_gudang_inspect.created_by' => $this->created_by,
            'trn_gudang_inspect.updated_at' => $this->updated_at,
            'trn_gudang_inspect.updated_by' => $this->updated_by,
            'jenis_gudang' => $this->jenis_gudang,
            'pfp_jenis_gudang' => $this->pfp_jenis_gudang,
            'keputusan_qc' => $this->keputusan_qc,
            'is_pemotongan' => $this->is_pemotongan,
            'is_hasil_mix' => $this->is_hasil_mix,
        ]);

        $query->andFilterWhere(['ilike', 'trn_gudang_inspect.no_lapak', $this->no_lapak])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.lot_lusi', $this->lot_lusi])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.lot_pakan', $this->lot_pakan])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.no_document', $this->no_document])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.operator', $this->operator])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.nomor_wo', $this->nomor_wo])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_gudang_inspect.color', $this->color])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
        ;

        return $dataProvider;
    }
}