<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKartuProsesMaklon;

/**
 * TrnKartuProsesMaklonSearch represents the model behind the search form of `common\models\ar\TrnKartuProsesMaklon`.
 */
class TrnKartuProsesMaklonSearch extends TrnKartuProsesMaklon
{
    public $vendorName;
    public $woNo;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'vendor_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'note', 'date', 'vendorName', 'woNo', 'dateRange'], 'safe'],
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
        $query = TrnKartuProsesMaklon::find();
        $query->joinWith(['wo', 'vendor']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_wo.date' => SORT_ASC],
            'desc' => ['trn_wo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['vendorName'] = [
            'asc' => ['mst_vendor.name' => SORT_ASC],
            'desc' => ['mst_vendor.name' => SORT_DESC],
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
                $query->andFilterWhere(['trn_kartu_proses_maklon.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kartu_proses_maklon.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kartu_proses_maklon.id' => $this->id,
            'trn_kartu_proses_maklon.sc_id' => $this->sc_id,
            'trn_kartu_proses_maklon.sc_greige_id' => $this->sc_greige_id,
            'trn_kartu_proses_maklon.mo_id' => $this->mo_id,
            'trn_kartu_proses_maklon.wo_id' => $this->wo_id,
            'trn_kartu_proses_maklon.vendor_id' => $this->vendor_id,
            'trn_kartu_proses_maklon.no_urut' => $this->no_urut,
            'trn_kartu_proses_maklon.date' => $this->date,
            'trn_kartu_proses_maklon.posted_at' => $this->posted_at,
            'trn_kartu_proses_maklon.approved_at' => $this->approved_at,
            'trn_kartu_proses_maklon.approved_by' => $this->approved_by,
            'trn_kartu_proses_maklon.status' => $this->status,
            'trn_kartu_proses_maklon.created_at' => $this->created_at,
            'trn_kartu_proses_maklon.created_by' => $this->created_by,
            'trn_kartu_proses_maklon.updated_at' => $this->updated_at,
            'trn_kartu_proses_maklon.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kartu_proses_maklon.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kartu_proses_maklon.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_vendor.name', $this->vendorName])
        ;

        return $dataProvider;
    }
}
