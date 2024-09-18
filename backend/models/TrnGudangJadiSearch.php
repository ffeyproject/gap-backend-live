<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGudangJadi;

/**
 * TrnGudangJadiSearch represents the model behind the search form of `common\models\ar\TrnGudangJadi`.
 */
class TrnGudangJadiSearch extends TrnGudangJadi
{
    public $marketingName;
    public $customerName;
    public $scNo;
    public $scDate;
    public $scOrientasi;
    public $scNoPo;
    public $scCurrencyId;
    public $scGreigeNamaKain;
    public $scGreigeProcessId;
    public $scGreigeGrade;

    public $moNo;

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
            [['id', 'jenis_gudang', 'wo_id', 'source', 'unit', 'qty', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [
                [
                    'source_ref', 'no', 'date', 'note', 'woNo', 'dateRange', 'scNo', 'scOrientasi', 'scDate', 'scNoPo', 'scCurrencyId', 'marketingName', 'customerName', 'color',
                    'scGreigeNamaKain', 'scGreigeProcessId', 'scGreigeGrade', 'moNo'
                ],
                'safe'
            ],
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
        $query = TrnGudangJadi::find();
        $query->joinWith(['wo.mo.scGreige.sc.cust', 'wo.mo.scGreige.sc.marketing', 'wo.mo.scGreige.greigeGroup']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'scDate' => SORT_ASC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['scDate'] = [
            'asc' => ['trn_sc.date' => SORT_ASC],
            'desc' => ['trn_sc.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_gudang_jadi.date' => SORT_ASC],
            'desc' => ['trn_gudang_jadi.date' => SORT_DESC],
        ];

        /*$dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['user.full_name' => SORT_ASC],
            'desc' => ['user.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];*/

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
                $query->andFilterWhere(['trn_gudang_jadi.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_gudang_jadi.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_gudang_jadi.id' => $this->id,
            'trn_gudang_jadi.jenis_gudang' => $this->jenis_gudang,
            'trn_gudang_jadi.wo_id' => $this->wo_id,
            'trn_gudang_jadi.source' => $this->source,
            'trn_gudang_jadi.unit' => $this->unit,
            'trn_gudang_jadi.qty' => $this->qty,
            'trn_gudang_jadi.no_urut' => $this->no_urut,
            'trn_gudang_jadi.date' => $this->date,
            'trn_gudang_jadi.status' => $this->status,
            'trn_gudang_jadi.created_at' => $this->created_at,
            'trn_gudang_jadi.created_by' => $this->created_by,
            'trn_gudang_jadi.updated_at' => $this->updated_at,
            'trn_gudang_jadi.updated_by' => $this->updated_by,
            'trn_gudang_jadi.color' => $this->color,
            'trn_sc.tipe_kontrak' => $this->scOrientasi,
            'trn_sc.currency' => $this->scCurrencyId,
            'trn_sc_greige.process' => $this->scGreigeProcessId,
            'trn_sc_greige.grade' => $this->scGreigeGrade,
        ]);

        $query->andFilterWhere(['ilike', 'trn_gudang_jadi.source_ref', $this->source_ref])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_gudang_jadi.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'user.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_sc.no_po', $this->scNoPo])
            ->andFilterWhere(['ilike', 'trn_sc.date', $this->scDate])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->scGreigeNamaKain])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
        ;

        //$query->orderBy(['trn_sc.no'=>SORT_ASC]);
        //$query->groupBy(['trn_sc.no', 'trn_gudang_jadi.id']);

        return $dataProvider;
    }
}
