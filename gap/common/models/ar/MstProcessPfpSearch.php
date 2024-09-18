<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\MstProcessPfp;

/**
 * MstProcessPfpSearch represents the model behind the search form of `common\models\ar\MstProcessPfp`.
 */
class MstProcessPfpSearch extends MstProcessPfp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order', 'max_pengulangan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['nama_proses'], 'safe'],
            [['tanggal', 'start', 'stop', 'no_mesin', 'shift_operator', 'temp', 'speed', 'waktu', 'program_number', 'ex_relax', 'ex_wr_oligomer', 'ex_dyeing', 'wr_pcnt', 'rpm', 'density', 'jamur', 'karat', 'over_feed', 'counter', 'lebar_jadi', 'info_kualitas', 'gangguan_produksi'], 'boolean'],
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
        $query = MstProcessPfp::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order' => SORT_ASC,
                    'id' => SORT_ASC,
                ]
            ],
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
            'order' => $this->order,
            'max_pengulangan' => $this->max_pengulangan,
            'tanggal' => $this->tanggal,
            'start' => $this->start,
            'stop' => $this->stop,
            'no_mesin' => $this->no_mesin,
            'shift_operator' => $this->shift_operator,
            'temp' => $this->temp,
            'speed' => $this->speed,
            'waktu' => $this->waktu,
            'program_number' => $this->program_number,
            'ex_relax' => $this->ex_relax,
            'ex_wr_oligomer' => $this->ex_wr_oligomer,
            'ex_dyeing' => $this->ex_dyeing,
            'wr_pcnt' => $this->wr_pcnt,
            'rpm' => $this->rpm,
            'density' => $this->density,
            'jamur' => $this->jamur,
            'karat' => $this->karat,
            'over_feed' => $this->over_feed,
            'counter' => $this->counter,
            'lebar_jadi' => $this->lebar_jadi,
            'info_kualitas' => $this->info_kualitas,
            'gangguan_produksi' => $this->gangguan_produksi,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'nama_proses', $this->nama_proses]);

        return $dataProvider;
    }
}
