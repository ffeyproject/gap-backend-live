<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TrnScSearch represents the model behind the search form of `common\models\ar\TrnSc`.
 */
class TrnScSearch extends TrnSc
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $customerName;
    public $marketingName;
    public $creatorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cust_id', 'jenis_order', 'currency', 'bank_acct_id', 'direktur_id', 'manager_id', 'marketing_id', 'no_urut', 'tipe_kontrak', 'pmt_term', 'ongkos_angkut', 'apv_dir_at', 'apv_mgr_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date', 'pmt_method', 'due_date', 'delivery_date', 'destination', 'packing', 'no_po', 'consignee_name', 'reject_note_dir', 'reject_note_mgr', 'notify_party', 'buyer_name_in_invoice', 'note', 'closed_note', 'batal_note'], 'safe'],
            [['jet_black'], 'boolean'],
            [['disc_grade_b', 'disc_piece_kecil'], 'number'],
            [['customerName', 'marketingName', 'creatorName', 'dateRange'], 'safe']
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
        $query = TrnSc::find()->joinWith(['cust', 'marketing as mkt', 'createdBy as cre']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'dateRange' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['mkt.full_name' => SORT_ASC],
            'desc' => ['mkt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => ['cre.full_name' => SORT_ASC],
            'desc' => ['cre.full_name' => SORT_DESC],
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
            'trn_sc.id' => $this->id,
            'trn_sc.cust_id' => $this->cust_id,
            'trn_sc.jenis_order' => $this->jenis_order,
            'trn_sc.currency' => $this->currency,
            'trn_sc.bank_acct_id' => $this->bank_acct_id,
            'trn_sc.direktur_id' => $this->direktur_id,
            'trn_sc.manager_id' => $this->manager_id,
            'trn_sc.marketing_id' => $this->marketing_id,
            'trn_sc.no_urut' => $this->no_urut,
            'trn_sc.tipe_kontrak' => $this->tipe_kontrak,
            'trn_sc.date' => $this->date,
            'trn_sc.pmt_term' => $this->pmt_term,
            'trn_sc.ongkos_angkut' => $this->ongkos_angkut,
            'trn_sc.due_date' => $this->due_date,
            'trn_sc.delivery_date' => $this->delivery_date,
            'trn_sc.jet_black' => $this->jet_black,
            'trn_sc.disc_grade_b' => $this->disc_grade_b,
            'trn_sc.disc_piece_kecil' => $this->disc_piece_kecil,
            'trn_sc.apv_dir_at' => $this->apv_dir_at,
            'trn_sc.apv_mgr_at' => $this->apv_mgr_at,
            'trn_sc.posted_at' => $this->posted_at,
            'trn_sc.closed_at' => $this->closed_at,
            'trn_sc.closed_by' => $this->closed_by,
            'trn_sc.batal_at' => $this->batal_at,
            'trn_sc.batal_by' => $this->batal_by,
            'trn_sc.status' => $this->status,
            'trn_sc.created_at' => $this->created_at,
            'trn_sc.created_by' => $this->created_by,
            'trn_sc.updated_at' => $this->updated_at,
            'trn_sc.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_sc.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_sc.pmt_method', $this->pmt_method])
            ->andFilterWhere(['ilike', 'trn_sc.destination', $this->destination])
            ->andFilterWhere(['ilike', 'trn_sc.packing', $this->packing])
            ->andFilterWhere(['ilike', 'trn_sc.no_po', $this->no_po])
            ->andFilterWhere(['ilike', 'trn_sc.consignee_name', $this->consignee_name])
            ->andFilterWhere(['ilike', 'trn_sc.reject_note_dir', $this->reject_note_dir])
            ->andFilterWhere(['ilike', 'trn_sc.reject_note_mgr', $this->reject_note_mgr])
            ->andFilterWhere(['ilike', 'trn_sc.notify_party', $this->notify_party])
            ->andFilterWhere(['ilike', 'trn_sc.buyer_name_in_invoice', $this->buyer_name_in_invoice])
            ->andFilterWhere(['ilike', 'trn_sc.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_sc.closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'trn_sc.batal_note', $this->batal_note])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'cre.full_name', $this->creatorName])
        ;

        return $dataProvider;
    }
}
