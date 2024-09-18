<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnSc;

/**
 * TrnScSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnSc`.
 */
class TrnScSearch extends TrnSc
{
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
        $query = TrnSc::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cust_id' => $this->cust_id,
            'jenis_order' => $this->jenis_order,
            'currency' => $this->currency,
            'bank_acct_id' => $this->bank_acct_id,
            'direktur_id' => $this->direktur_id,
            'manager_id' => $this->manager_id,
            'marketing_id' => $this->marketing_id,
            'no_urut' => $this->no_urut,
            'tipe_kontrak' => $this->tipe_kontrak,
            'date' => $this->date,
            'pmt_term' => $this->pmt_term,
            'ongkos_angkut' => $this->ongkos_angkut,
            'due_date' => $this->due_date,
            'delivery_date' => $this->delivery_date,
            'jet_black' => $this->jet_black,
            'disc_grade_b' => $this->disc_grade_b,
            'disc_piece_kecil' => $this->disc_piece_kecil,
            'apv_dir_at' => $this->apv_dir_at,
            'apv_mgr_at' => $this->apv_mgr_at,
            'posted_at' => $this->posted_at,
            'closed_at' => $this->closed_at,
            'closed_by' => $this->closed_by,
            'batal_at' => $this->batal_at,
            'batal_by' => $this->batal_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'no', $this->no])
            ->andFilterWhere(['ilike', 'pmt_method', $this->pmt_method])
            ->andFilterWhere(['ilike', 'destination', $this->destination])
            ->andFilterWhere(['ilike', 'packing', $this->packing])
            ->andFilterWhere(['ilike', 'no_po', $this->no_po])
            ->andFilterWhere(['ilike', 'consignee_name', $this->consignee_name])
            ->andFilterWhere(['ilike', 'reject_note_dir', $this->reject_note_dir])
            ->andFilterWhere(['ilike', 'reject_note_mgr', $this->reject_note_mgr])
            ->andFilterWhere(['ilike', 'notify_party', $this->notify_party])
            ->andFilterWhere(['ilike', 'buyer_name_in_invoice', $this->buyer_name_in_invoice])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'batal_note', $this->batal_note]);

        return $dataProvider;
    }
}
