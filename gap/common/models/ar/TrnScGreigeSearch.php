<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnScGreige;

/**
 * TrnScGreigeSearch represents the model behind the search form of `common\models\ar\TrnScGreige`.
 */
class TrnScGreigeSearch extends TrnScGreige
{
    public $nomorSc;
    public $greigeGroupNamaKain;
    public $from_date;
    public $to_date;
    public $dateRange;
    public $scTipeKontrak;
    public $scCustomerName;
    public $scMarketingName;
    public $scNoPo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'greige_group_id', 'process', 'lebar_kain', 'grade', 'no_urut_order_greige', 'order_grege_approved_at', 'order_grege_approved_at_dir', 'order_grege_approved_by'], 'integer'],
            [
                [
                    'merek', 'piece_length', 'price_param', 'woven_selvedge', 'note', 'closing_note', 'no_order_greige', 'order_greige_note', 'nomorSc', 'greigeGroupNamaKain',
                    'order_grege_approval_note', 'order_grege_approval_note_dir', 'dateRange', 'scTipeKontrak', 'scCustomerName', 'scMarketingName', 'scNoPo',
                ],
                'safe'
            ],
            [['unit_price', 'qty'], 'number'],
            [['closed', 'order_grege_approved', 'order_grege_approved_dir'], 'boolean'],
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
        $query = TrnScGreige::find()->joinWith(['sc.cust', 'greigeGroup', 'sc.marketing mkt']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['nomorSc'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scTipeKontrak'] = [
            'asc' => ['trn_sc.tipe_kontrak' => SORT_ASC],
            'desc' => ['trn_sc.tipe_kontrak' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scCustomerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scMarketingName'] = [
            'asc' => ['mkt.full_name' => SORT_ASC],
            'desc' => ['mkt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nomorSc'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scNoPo'] = [
            'asc' => ['trn_sc.no_po' => SORT_ASC],
            'desc' => ['trn_sc.no_po' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_sc.date' => SORT_ASC],
            'desc' => ['trn_sc.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_sc.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_sc.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_sc_greige.id' => $this->id,
            'trn_sc_greige.sc_id' => $this->sc_id,
            'trn_sc_greige.greige_group_id' => $this->greige_group_id,
            'trn_sc_greige.process' => $this->process,
            'trn_sc_greige.lebar_kain' => $this->lebar_kain,
            'trn_sc_greige.grade' => $this->grade,
            'trn_sc_greige.unit_price' => $this->unit_price,
            'trn_sc_greige.qty' => $this->qty,
            'trn_sc_greige.closed' => $this->closed,
            'trn_sc_greige.no_urut_order_greige' => $this->no_urut_order_greige,
            'trn_sc_greige.order_grege_approved' => $this->order_grege_approved,
            'trn_sc_greige.order_grege_approved_dir' => $this->order_grege_approved_dir,
            'trn_sc.tipe_kontrak' => $this->scTipeKontrak,
        ]);

        $query->andFilterWhere(['ilike', 'trn_sc_greige.merek', $this->merek])
            ->andFilterWhere(['ilike', 'trn_sc_greige.piece_length', $this->piece_length])
            ->andFilterWhere(['ilike', 'trn_sc_greige.price_param', $this->price_param])
            ->andFilterWhere(['ilike', 'trn_sc_greige.woven_selvedge', $this->woven_selvedge])
            ->andFilterWhere(['ilike', 'trn_sc_greige.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_sc_greige.closing_note', $this->closing_note])
            ->andFilterWhere(['ilike', 'trn_sc_greige.no_order_greige', $this->no_order_greige])
            ->andFilterWhere(['ilike', 'trn_sc_greige.order_greige_note', $this->order_greige_note])
            ->andFilterWhere(['ilike', 'trn_sc_greige.order_grege_approved_at', $this->order_grege_approved_at])
            ->andFilterWhere(['ilike', 'trn_sc_greige.order_grege_approved_by', $this->order_grege_approved_by])
            ->andFilterWhere(['ilike', 'trn_sc_greige.order_grege_approved_at_dir', $this->order_grege_approved_at_dir])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->nomorSc])
            ->andFilterWhere(['ilike', 'trn_sc.no_po', $this->scNoPo])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->scCustomerName])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->scMarketingName])
        ;

        return $dataProvider;
    }
}
