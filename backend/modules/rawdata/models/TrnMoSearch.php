<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnMo;

/**
 * TrnMoSearch represents the model behind the search form of `backend\modules\rawdata\models\TrnMo`.
 */
class TrnMoSearch extends TrnMo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'process', 'approval_id', 'approved_at', 'no_urut', 'border_size', 'block_size', 'joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'jenis_gudang', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date', 're_wo', 'design', 'article', 'strike_off', 'sulam_pinggir', 'face_stamping', 'selvedge_stamping', 'selvedge_continues', 'side_band', 'tag', 'hanger', 'label', 'folder', 'album', 'arsip', 'piece_length', 'est_produksi', 'est_packing', 'target_shipment', 'closed_note', 'reject_notes', 'batal_note', 'note'], 'safe'],
            [['heat_cut', 'foil', 'joint', 'jet_black'], 'boolean'],
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
        $query = TrnMo::find();

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
            'sc_id' => $this->sc_id,
            'sc_greige_id' => $this->sc_greige_id,
            'process' => $this->process,
            'approval_id' => $this->approval_id,
            'approved_at' => $this->approved_at,
            'no_urut' => $this->no_urut,
            'date' => $this->date,
            'heat_cut' => $this->heat_cut,
            'border_size' => $this->border_size,
            'block_size' => $this->block_size,
            'foil' => $this->foil,
            'joint' => $this->joint,
            'joint_qty' => $this->joint_qty,
            'packing_method' => $this->packing_method,
            'shipping_method' => $this->shipping_method,
            'shipping_sorting' => $this->shipping_sorting,
            'plastic' => $this->plastic,
            'jet_black' => $this->jet_black,
            'est_produksi' => $this->est_produksi,
            'est_packing' => $this->est_packing,
            'target_shipment' => $this->target_shipment,
            'jenis_gudang' => $this->jenis_gudang,
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
            ->andFilterWhere(['ilike', 're_wo', $this->re_wo])
            ->andFilterWhere(['ilike', 'design', $this->design])
            ->andFilterWhere(['ilike', 'article', $this->article])
            ->andFilterWhere(['ilike', 'strike_off', $this->strike_off])
            ->andFilterWhere(['ilike', 'sulam_pinggir', $this->sulam_pinggir])
            ->andFilterWhere(['ilike', 'face_stamping', $this->face_stamping])
            ->andFilterWhere(['ilike', 'selvedge_stamping', $this->selvedge_stamping])
            ->andFilterWhere(['ilike', 'selvedge_continues', $this->selvedge_continues])
            ->andFilterWhere(['ilike', 'side_band', $this->side_band])
            ->andFilterWhere(['ilike', 'tag', $this->tag])
            ->andFilterWhere(['ilike', 'hanger', $this->hanger])
            ->andFilterWhere(['ilike', 'label', $this->label])
            ->andFilterWhere(['ilike', 'folder', $this->folder])
            ->andFilterWhere(['ilike', 'album', $this->album])
            ->andFilterWhere(['ilike', 'arsip', $this->arsip])
            ->andFilterWhere(['ilike', 'piece_length', $this->piece_length])
            ->andFilterWhere(['ilike', 'closed_note', $this->closed_note])
            ->andFilterWhere(['ilike', 'reject_notes', $this->reject_notes])
            ->andFilterWhere(['ilike', 'batal_note', $this->batal_note])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
