<?php

namespace common\models\rekap;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnWo;

/**
 * TrnWoSearch represents the model behind the search form of `common\models\ar\TrnWo`.
 */
class RekapWoTotalSearch extends TrnWo
{
    public $from_date;
    public $to_date;
    public $dateRange;
    public $tipeKontrak;
    public $proccess;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['dateRange', 'required'],
            [['jenis_order', 'status'], 'integer'],
            [['proccess'], 'safe']
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
        $query = TrnWo::find();
        $query->joinWith(['scGreige.sc']);

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
            'asc' => ['trn_wo.date' => SORT_ASC],
            'desc' => ['trn_wo.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['proccess'] = [
            'asc' => ['trn_sc_greige.process' => SORT_ASC],
            'desc' => ['trn_sc_greige.process' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tipeKontrak'] = [
            'asc' => ['trn_sc.tipe_kontrak' => SORT_ASC],
            'desc' => ['trn_sc.tipe_kontrak' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['trn_wo.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'trn_wo.date', $this->from_date, $this->to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_wo.status' => $this->status,
            'trn_wo.jenis_order' => $this->jenis_order,
            /*'trn_wo.id' => $this->id,
            'trn_wo.sc_id' => $this->sc_id,
            'trn_wo.sc_greige_id' => $this->sc_greige_id,
            'trn_wo.mo_id' => $this->mo_id,
            'trn_wo.greige_id' => $this->greige_id,
            'trn_wo.mengetahui_id' => $this->mengetahui_id,
            'trn_wo.apv_mengetahui_at' => $this->apv_mengetahui_at,
            'trn_wo.no_urut' => $this->no_urut,
            'trn_wo.date' => $this->date,
            'trn_wo.marketing_id' => $this->marketing_id,
            'trn_wo.apv_marketing_at' => $this->apv_marketing_at,
            'trn_wo.posted_at' => $this->posted_at,
            'trn_wo.closed_at' => $this->closed_at,
            'trn_wo.closed_by' => $this->closed_by,
            'trn_wo.batal_at' => $this->batal_at,
            'trn_wo.batal_by' => $this->batal_by,
            'trn_wo.created_at' => $this->created_at,
            'trn_wo.created_by' => $this->created_by,
            'trn_wo.updated_at' => $this->updated_at,
            'trn_wo.updated_by' => $this->updated_by,
            'handling_id' => $this->handling_id,
            'trn_sc_greige.process' => $this->proccess,
            'trn_sc.tipe_kontrak' => $this->tipeKontrak,
            'validasi_stock' => $this->validasi_stock,*/
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_sc_greige.process' => $this->proccess,
            'trn_sc.tipe_kontrak' => $this->tipeKontrak,
        ]);

        return $dataProvider;
    }
}
