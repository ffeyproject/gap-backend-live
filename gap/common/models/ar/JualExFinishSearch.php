<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\JualExFinish;

/**
 * JualExFinishSearch represents the model behind the search form of `common\models\ar\JualExFinish`.
 */
class JualExFinishSearch extends JualExFinish
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_urut', 'id', 'jenis_gudang', 'customer_id', 'grade', 'ongkir', 'jenis_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['harga'], 'number'],
            [['no', 'no_po', 'pembayaran', 'tanggal_pengiriman', 'komisi', 'keterangan'], 'safe'],
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
        $query = JualExFinish::find();

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
            'no_urut' => $this->no_urut,
            'jenis_gudang' => $this->jenis_gudang,
            'customer_id' => $this->customer_id,
            'grade' => $this->grade,
            'harga' => $this->harga,
            'ongkir' => $this->ongkir,
            'tanggal_pengiriman' => $this->tanggal_pengiriman,
            'jenis_order' => $this->jenis_order,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'pembayaran', $this->pembayaran])
            ->andFilterWhere(['ilike', 'komisi', $this->komisi])
            ->andFilterWhere(['ilike', 'keterangan', $this->keterangan])
            ->andFilterWhere(['ilike', 'no', $this->no])
        ;

        return $dataProvider;
    }
}
