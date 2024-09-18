<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnMo;

/**
 * TrnMoSearch represents the model behind the search form of `common\models\ar\TrnMo`.
 */
class TrnMoSearch extends TrnMo
{
    public $from_date;
    public $to_date;
    public $dateRange;

    public $customerName;
    public $nomorSc;
    public $scGreigeNamaKain;
    public $marketingName;
    public $creatorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'process', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date', 're_wo', 'design', 'article', 'strike_off', 'sulam_pinggir', 'face_stamping', 'selvedge_stamping', 'selvedge_continues', 'side_band', 'tag', 'hanger', 'label', 'folder', 'album', 'arsip', 'piece_length', 'est_produksi', 'est_packing', 'target_shipment', 'closed_note', 'reject_notes', 'batal_note', 'note'], 'safe'],
            [['heat_cut', 'foil', 'joint', 'jet_black'], 'boolean'],
            [['no_lab_dip', 'dateRange', 'customerName', 'marketingName', 'nomorSc', 'scGreigeNamaKain', 'creatorName'], 'safe']
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
        $query = TrnMo::find()->joinWith(['sc.cust', 'sc.marketing as mkt', 'scGreige.greigeGroup', 'createdBy as crt']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'customerName' => SORT_ASC,
                    'dateRange' => SORT_DESC,
                    //'nomorSc' => SORT_ASC,
                    //'scId' => SORT_ASC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nomorSc'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['scGreigeNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['mkt.full_name' => SORT_ASC],
            'desc' => ['mkt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => ['crt.full_name' => SORT_ASC],
            'desc' => ['crt.full_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['trn_mo.date' => SORT_ASC],
            'desc' => ['trn_mo.date' => SORT_DESC],
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
                $query->andFilterWhere(['trn_mo.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_mo.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_mo.id' => $this->id,
            'trn_mo.sc_id' => $this->sc_id,
            'trn_mo.sc_greige_id' => $this->sc_greige_id,
            'trn_mo.process' => $this->process,
            'trn_mo.approval_id' => $this->approval_id,
            'trn_mo.approved_at' => $this->approved_at,
            'trn_mo.no_urut' => $this->no_urut,
            'trn_mo.date' => $this->date,
            'trn_mo.heat_cut' => $this->heat_cut,
            'trn_mo.border_size' => $this->border_size,
            'trn_mo.block_size' => $this->block_size,
            'trn_mo.foil' => $this->foil,
            'trn_mo.joint' => $this->joint,
            'trn_mo.joint_qty' => $this->joint_qty,
            'trn_mo.packing_method' => $this->packing_method,
            'trn_mo.shipping_method' => $this->shipping_method,
            'trn_mo.shipping_sorting' => $this->shipping_sorting,
            'trn_mo.plastic' => $this->plastic,
            'trn_mo.jet_black' => $this->jet_black,
            'trn_mo.est_produksi' => $this->est_produksi,
            'trn_mo.est_packing' => $this->est_packing,
            'trn_mo.target_shipment' => $this->target_shipment,
            'trn_mo.posted_at' => $this->posted_at,
            'trn_mo.closed_at' => $this->closed_at,
            'trn_mo.closed_by' => $this->closed_by,
            'trn_mo.batal_at' => $this->batal_at,
            'trn_mo.batal_by' => $this->batal_by,
            'trn_mo.status' => $this->status,
            'trn_mo.created_at' => $this->created_at,
            'trn_mo.created_by' => $this->created_by,
            'trn_mo.updated_at' => $this->updated_at,
            'trn_mo.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_mo.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_mo.re_wo', $this->re_wo])
            ->andFilterWhere(['ilike', 'trn_mo.design', $this->design])
            ->andFilterWhere(['ilike', 'trn_mo.article', $this->article])
            ->andFilterWhere(['ilike', 'trn_mo.strike_off', $this->strike_off])
            ->andFilterWhere(['ilike', 'trn_mo.sulam_pinggir', $this->sulam_pinggir])
            ->andFilterWhere(['ilike', 'trn_mo.face_stamping', $this->face_stamping])
            ->andFilterWhere(['ilike', 'trn_mo.selvedge_stamping', $this->selvedge_stamping])
            ->andFilterWhere(['ilike', 'trn_mo.selvedge_continues', $this->selvedge_continues])
            ->andFilterWhere(['ilike', 'trn_mo.side_band', $this->side_band])
            ->andFilterWhere(['ilike', 'trn_mo.tag', $this->tag])
            ->andFilterWhere(['ilike', 'trn_mo.hanger', $this->hanger])
            ->andFilterWhere(['ilike', 'trn_mo.label', $this->label])
            ->andFilterWhere(['ilike', 'trn_mo.folder', $this->folder])
            ->andFilterWhere(['ilike', 'trn_mo.album', $this->album])
            ->andFilterWhere(['ilike', 'trn_mo.arsip', $this->arsip])
            ->andFilterWhere(['ilike', 'trn_mo.piece_length', $this->piece_length])
            ->andFilterWhere(['ilike', 'trn_mo.closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'trn_mo.reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'trn_mo.batal_note', $this->batal_note])
            ->andFilterWhere(['ilike', 'trn_mo.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_mo.no_lab_dip', $this->no_lab_dip])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->nomorSc])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->scGreigeNamaKain])
            ->andFilterWhere(['ilike', 'mkt.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'crt.full_name', $this->creatorName])
        ;

        return $dataProvider;
    }
}
