<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesPrinting;

/**
 * TrnKartuProsesPrintingSearch represents the model behind the search form of `common\models\ar\TrnKartuProsesPrinting`.
 */
class TrnKartuProsesPrintingSearch extends TrnKartuProsesPrinting
{
    public $woNo;
    public $moColorColor;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'note', 'date', 'memo_pg', 'memo_pg_no', 'reject_notes', 'nomor_kartu'], 'safe'],
            [['woNo', 'dateRange', 'moColorColor'], 'safe']
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
        $query = TrnKartuProsesPrinting::find();
        $query->joinWith(['wo', 'woColor.moColor']);

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
            'asc' => ['trn_kartu_proses_printing.date' => SORT_ASC],
            'desc' => ['trn_kartu_proses_printing.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['moColorColor'] = [
            'asc' => ['trn_mo_color.color' => SORT_ASC],
            'desc' => ['trn_mo_color.color' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kartu_proses_printing.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kartu_proses_printing.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kartu_proses_printing.id' => $this->id,
            'trn_kartu_proses_printing.sc_id' => $this->sc_id,
            'trn_kartu_proses_printing.sc_greige_id' => $this->sc_greige_id,
            'trn_kartu_proses_printing.mo_id' => $this->mo_id,
            'trn_kartu_proses_printing.wo_id' => $this->wo_id,
            'trn_kartu_proses_printing.kartu_proses_id' => $this->kartu_proses_id,
            'trn_kartu_proses_printing.no_urut' => $this->no_urut,
            'trn_kartu_proses_printing.asal_greige' => $this->asal_greige,
            'trn_kartu_proses_printing.date' => $this->date,
            'trn_kartu_proses_printing.posted_at' => $this->posted_at,
            'trn_kartu_proses_printing.approved_at' => $this->approved_at,
            'trn_kartu_proses_printing.approved_by' => $this->approved_by,
            'trn_kartu_proses_printing.status' => $this->status,
            'trn_kartu_proses_printing.created_at' => $this->created_at,
            'trn_kartu_proses_printing.created_by' => $this->created_by,
            'trn_kartu_proses_printing.updated_at' => $this->updated_at,
            'trn_kartu_proses_printing.updated_by' => $this->updated_by,
            'trn_kartu_proses_printing.memo_pg_at' => $this->memo_pg_at,
            'trn_kartu_proses_printing.memo_pg_by' => $this->memo_pg_by,
            'trn_kartu_proses_printing.delivered_at' => $this->delivered_at,
            'trn_kartu_proses_printing.delivered_by' => $this->delivered_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_printing.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.no_proses', $this->no_proses])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.dikerjakan_oleh', $this->dikerjakan_oleh])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.lusi', $this->lusi])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.pakan', $this->pakan])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.memo_pg', $this->memo_pg])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.memo_pg_no', $this->memo_pg_no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_printing.nomor_kartu', $this->nomor_kartu])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'trn_mo_color.color', $this->moColorColor])
        ;

        return $dataProvider;
    }
}
