<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnStockGreige;
use yii\db\Expression;

class TrnStockGreigeSearch extends TrnStockGreige
{
    public $greigeNamaKain;

    public function rules()
    {
        return [
            [['id', 'greige_id'], 'integer'],
            [['lot_lusi', 'lot_pakan', 'no_document'], 'string'],
            [['panjang_m'], 'number'],
            [['greigeNamaKain'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TrnStockGreige::find()
            ->alias('s')
            ->joinWith('greige g')
            ->where(['s.status' => TrnStockGreige::STATUS_VALID]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        // sorting nama kain
        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['g.nama_kain' => SORT_ASC],
            'desc' => ['g.nama_kain' => SORT_DESC],
        ];

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            's.id' => $this->id,
            's.no_document' => $this->no_document,
            's.lot_lusi' => $this->lot_lusi,
            's.lot_pakan' => $this->lot_pakan,
            's.panjang_m' => $this->panjang_m,
        ]);

        if (!empty($this->greigeNamaKain)) {
            $query->andWhere(['like', 'g.nama_kain', $this->greigeNamaKain]);
        }

        return $dataProvider;
    }

}