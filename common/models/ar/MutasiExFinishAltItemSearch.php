<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MutasiExFinishAltItem;

/**
 * MutasiExFinishAltItemSearch represents the model behind the search form of `common\models\ar\MutasiExFinishAltItem`.
 */
class MutasiExFinishAltItemSearch extends MutasiExFinishAltItem
{
    public $greigeNamaKain;
    public $wo_no;
    public $no_referensi;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mutasi_id', 'gudang_jadi_id', 'grade', 'status'], 'integer'],
            [['qty'], 'number'],
            [['greigeNamaKain', 'dateRange', 'wo_no', 'no_referensi'], 'safe'],
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
        $query = MutasiExFinishAltItem::find()->joinWith(['gudangJadi.wo.greige', 'mutasi']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['mutasi_ex_finish_alt.created_at' => SORT_ASC],
            'desc' => ['mutasi_ex_finish_alt.created_at' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
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

            $query->andFilterWhere(['between', 'mutasi_ex_finish_alt.created_at', strtotime($this->from_date), strtotime($this->to_date.' 23:59:59')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mutasi_ex_finish_alt_item.id' => $this->id,
            'mutasi_ex_finish_alt_item.mutasi_id' => $this->mutasi_id,
            'mutasi_ex_finish_alt_item.gudang_jadi_id' => $this->gudang_jadi_id,
            'mutasi_ex_finish_alt_item.grade' => $this->grade,
            'mutasi_ex_finish_alt_item.qty' => $this->qty,
            'mutasi_ex_finish_alt_item.status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->wo_no])
            ->andFilterWhere(['ilike', 'mutasi_ex_finish_alt.no_referensi', $this->no_referensi])
        ;

        return $dataProvider;
    }
}
