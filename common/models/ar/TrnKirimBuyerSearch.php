<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnKirimBuyer;

/**
 * TrnKirimBuyerSearch represents the model behind the search form of `common\models\ar\TrnKirimBuyer`.
 */
class TrnKirimBuyerSearch extends TrnKirimBuyer
{
    public $scNo;
    public $moNo;
    public $woNo;
    public $custName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id','unit', 'header_id'], 'integer'],
            [['nama_kain_alias', 'note', 'scNo', 'moNo', 'woNo', 'custName'], 'safe'],
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
        $query = TrnKirimBuyer::find();
        $query->joinWith(['wo', 'mo', 'sc.cust']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_kirim_buyer.id' => $this->id,
            'trn_kirim_buyer.sc_id' => $this->sc_id,
            'trn_kirim_buyer.sc_greige_id' => $this->sc_greige_id,
            'trn_kirim_buyer.mo_id' => $this->mo_id,
            'trn_kirim_buyer.wo_id' => $this->wo_id,
            'trn_kirim_buyer.unit' => $this->unit,
            'trn_kirim_buyer.header_id' => $this->header_id,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_buyer.nama_kain_alias', $this->nama_kain_alias])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_mo.no', $this->moNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->custName])
        ;

        return $dataProvider;
    }
}
