<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\PfpKeluarVerpacking;

/**
 * PfpKeluarVerpackingSearch represents the model behind the search form of `common\models\ar\PfpKeluarVerpacking`.
 */
class PfpKeluarVerpackingSearch extends PfpKeluarVerpacking
{
    public $pfpKeluarNo;
    public $dateRangeKirim;
    public $dateRangeInspect;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pfp_keluar_id', 'greige_id', 'no_urut', 'jenis', 'satuan', 'vendor_id', 'wo_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['no', 'tgl_kirim', 'tgl_inspect', 'note', 'vendor_address', 'dateRangeKirim', 'dateRangeInspect', 'pfpKeluarNo'], 'safe'],
            [['send_to_vendor'], 'boolean'],
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
        $query = PfpKeluarVerpacking::find()->joinWith(['pfpKeluar']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'dateRangeInspect' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['pfpKeluarNo'] = [
            'asc' => ['trn_pfp_keluar.no' => SORT_ASC],
            'desc' => ['trn_pfp_keluar.no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeKirim'] = [
            'asc' => ['pfp_keluar_verpacking.tgl_kirim' => SORT_ASC],
            'desc' => ['pfp_keluar_verpacking.tgl_kirim' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRangeInspect'] = [
            'asc' => ['pfp_keluar_verpacking.tgl_inspect' => SORT_ASC],
            'desc' => ['pfp_keluar_verpacking.tgl_inspect' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->dateRangeKirim)){
            $from_date = substr($this->dateRangeKirim, 0, 10);
            $to_date = substr($this->dateRangeKirim, 14);

            if($from_date == $to_date){
                $query->andFilterWhere(['pfp_keluar_verpacking.tgl_kirim' => $from_date]);
            }else{
                $query->andFilterWhere(['between', 'pfp_keluar_verpacking.tgl_kirim', $from_date, $to_date]);
            }
        }

        if(!empty($this->dateRangeInspect)){
            $from_date = substr($this->dateRangeInspect, 0, 10);
            $to_date = substr($this->dateRangeInspect, 14);

            if($from_date == $to_date){
                $query->andFilterWhere(['pfp_keluar_verpacking.tgl_inspect' => $from_date]);
            }else{
                $query->andFilterWhere(['between', 'pfp_keluar_verpacking.tgl_inspect', $from_date, $to_date]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pfp_keluar_verpacking.id' => $this->id,
            'pfp_keluar_verpacking.pfp_keluar_id' => $this->pfp_keluar_id,
            'pfp_keluar_verpacking.greige_id' => $this->greige_id,
            'pfp_keluar_verpacking.no_urut' => $this->no_urut,
            'pfp_keluar_verpacking.jenis' => $this->jenis,
            'pfp_keluar_verpacking.satuan' => $this->satuan,
            'pfp_keluar_verpacking.send_to_vendor' => $this->send_to_vendor,
            'pfp_keluar_verpacking.vendor_id' => $this->vendor_id,
            'pfp_keluar_verpacking.wo_id' => $this->wo_id,
            'pfp_keluar_verpacking.status' => $this->status,
            'pfp_keluar_verpacking.created_at' => $this->created_at,
            'pfp_keluar_verpacking.created_by' => $this->created_by,
            'pfp_keluar_verpacking.updated_at' => $this->updated_at,
            'pfp_keluar_verpacking.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'pfp_keluar_verpacking.no', $this->no])
            ->andFilterWhere(['ilike', 'trn_pfp_keluar.no', $this->pfpKeluarNo])
            //->andFilterWhere(['ilike', 'pfp_keluar_verpacking.note', $this->note])
            //->andFilterWhere(['ilike', 'pfp_keluar_verpacking.vendor_address', $this->vendor_address])
        ;

        return $dataProvider;
    }
}
