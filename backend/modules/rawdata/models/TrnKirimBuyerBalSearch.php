<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKirimBuyerBal;

class TrnKirimBuyerBalSearch extends TrnKirimBuyerBal
{
    public $trnKirimBuyerHeaderId; // optional kolom virtual dari relasi jika diperlukan

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trn_kirim_buyer_id', 'header_id'], 'integer'],
            [['no_bal'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass parent implementation in scenarios()
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
        $query = TrnKirimBuyerBal::find()->alias('bal');

        // Join dengan trn_kirim_buyer jika kamu perlu akses relasi di sort/filter
        $query->joinWith(['trnKirimBuyer kb']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Optional: sort tambahan jika perlu dari relasi
        $dataProvider->sort->attributes['trn_kirim_buyer_id'] = [
            'asc' => ['kb.id' => SORT_ASC],
            'desc' => ['kb.id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'bal.id' => $this->id,
            'bal.trn_kirim_buyer_id' => $this->trn_kirim_buyer_id,
            'bal.header_id' => $this->header_id,
        ]);

        $query->andFilterWhere(['ilike', 'bal.no_bal', $this->no_bal]);

        return $dataProvider;
    }
}