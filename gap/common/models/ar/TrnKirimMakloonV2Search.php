<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKirimMakloonV2;

/**
 * TrnKirimMakloonV2Search represents the model behind the search form of `common\models\ar\TrnKirimMakloonV2`.
 */
class TrnKirimMakloonV2Search extends TrnKirimMakloonV2
{
    public $dateRange;

    public $scNo;
    public $moNo;
    public $woNo;
    public $custName;
    public $vendorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'vendor_id', 'mo_id', 'wo_id', 'no_urut', 'unit', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['date', 'no', 'note', 'penerima', 'dateRange', 'scNo', 'moNo', 'woNo', 'custName', 'vendorName'], 'safe'],
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
        $query = TrnKirimMakloonV2::find()->joinWith(['wo', 'mo', 'sc.cust', 'vendor']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['moNo'] = [
            'asc' => ['trn_mo.no' => SORT_ASC],
            'desc' => ['trn_mo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['custName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
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
            $from_date = substr($this->dateRange, 0, 10);
            $to_date = substr($this->dateRange, 14);

            if($from_date == $to_date){
                $query->andFilterWhere(['trn_kirim_makloon_v2.date' => $from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_kirim_makloon_v2.date', $from_date, $to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kirim_makloon_v2.id' => $this->id,
            'trn_kirim_makloon_v2.sc_id' => $this->sc_id,
            'trn_kirim_makloon_v2.sc_greige_id' => $this->sc_greige_id,
            'trn_kirim_makloon_v2.vendor_id' => $this->vendor_id,
            'trn_kirim_makloon_v2.mo_id' => $this->mo_id,
            'trn_kirim_makloon_v2.wo_id' => $this->wo_id,
            'trn_kirim_makloon_v2.date' => $this->date,
            'trn_kirim_makloon_v2.no_urut' => $this->no_urut,
            'trn_kirim_makloon_v2.unit' => $this->unit,
            'trn_kirim_makloon_v2.status' => $this->status,
            'trn_kirim_makloon_v2.created_at' => $this->created_at,
            'trn_kirim_makloon_v2.created_by' => $this->created_by,
            'trn_kirim_makloon_v2.updated_at' => $this->updated_at,
            'trn_kirim_makloon_v2.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.penerima', $this->penerima])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.no', $this->woNo])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.name', $this->custName])
            ->andFilterWhere(['ilike', 'trn_kirim_makloon_v2.name', $this->vendorName])
        ;

        return $dataProvider;
    }
}
