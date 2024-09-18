<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\PfpKeluarVerpackingItem;

/**
 * PfpKeluarVerpackingItemSearch represents the model behind the search form of `common\models\ar\PfpKeluarVerpackingItem`.
 */
class PfpKeluarVerpackingItemSearch extends PfpKeluarVerpackingItem
{
    public $greigeId;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pfp_keluar_verpacking_id', 'status', 'greigeId'], 'integer'],
            [['ukuran', 'join_piece'], 'number'],
            [['keterangan'], 'safe'],
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
        $query = PfpKeluarVerpackingItem::find()->joinWith('pfpKeluarVerpacking.pfpKeluar');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['greigeId'] = [
            'asc' => ['pfp_keluar_verpacking.greige_id' => SORT_ASC],
            'desc' => ['pfp_keluar_verpacking.greige_id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pfp_keluar_verpacking_item.id' => $this->id,
            'pfp_keluar_verpacking_item.status' => $this->status,
            'pfp_keluar_verpacking_item.pfp_keluar_verpacking_id' => $this->pfp_keluar_verpacking_id,
            'pfp_keluar_verpacking_item.ukuran' => $this->ukuran,
            'pfp_keluar_verpacking_item.join_piece' => $this->join_piece,
            'pfp_keluar_verpacking.greige_id' => $this->greigeId,
        ]);

        //$query->andFilterWhere(['ilike', 'pfp_keluar_verpacking_item.keterangan', $this->keterangan]);

        return $dataProvider;
    }
}
