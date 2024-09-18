<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnScKomisi;

/**
 * TrnScKomisiSearch represents the model behind the search form of `common\models\ar\TrnScKomisi`.
 */
class TrnScKomisiSearch extends TrnScKomisi
{
    public $nomorSc;
    public $namaAgen;
    public $greigeGroupNamaKain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sc_id', 'sc_agen_id', 'sc_greige_id', 'tipe_komisi'], 'integer'],
            [['komisi_amount'], 'number'],
            [['nomorSc', 'namaAgen', 'greigeGroupNamaKain'], 'safe'],
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
        $query = TrnScKomisi::find()->joinWith(['scAgen.sc', 'scGreige.greigeGroup']);

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

        $dataProvider->sort->attributes['namaAgen'] = [
            'asc' => ['trn_sc_agen.nama_agen' => SORT_ASC],
            'desc' => ['trn_sc_agen.nama_agen' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeGroupNamaKain'] = [
            'asc' => ['mst_greige_group.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige_group.nama_kain' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'trn_sc_komisi.id' => $this->id,
            'trn_sc_komisi.sc_id' => $this->sc_id,
            'trn_sc_komisi.sc_agen_id' => $this->sc_agen_id,
            'trn_sc_komisi.sc_greige_id' => $this->sc_greige_id,
            'trn_sc_komisi.tipe_komisi' => $this->tipe_komisi,
            'trn_sc_komisi.komisi_amount' => $this->komisi_amount,
        ]);

        $query->andFilterWhere(['ilike', 'trn_sc.no', $this->nomorSc])
            ->andFilterWhere(['ilike', 'trn_sc_agen.nama_agen', $this->namaAgen])
            ->andFilterWhere(['ilike', 'mst_greige_group.nama_kain', $this->greigeGroupNamaKain])
        ;

        return $dataProvider;
    }
}
