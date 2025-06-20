<?php

namespace backend\modules\rawdata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rawdata\models\TrnKirimBuyerItem;

class TrnKirimBuyerItemSearch extends TrnKirimBuyerItem
{
    public $header_id;
    public $woNo;
    public $dateRange;
    public $scNo;
    public $marketingName;
    public $customerName;

    private $from_date;
    private $to_date;
    public $noLot;


    public function rules()
    {
        return [
            [['id', 'kirim_buyer_id', 'stock_id', 'bal_id', 'header_id'], 'integer'],
            [['qty'], 'number'],
            [['note', 'no_bal', 'woNo', 'scNo', 'dateRange', 'marketingName', 'customerName', 'noLot'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TrnKirimBuyerItem::find();
        $query->joinWith([
            'stock.wo.mo.scGreige.sc.cust',
            'stock.wo.mo.scGreige.sc.marketing',
            'kirimBuyer',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Sorting untuk kolom virtual
        $dataProvider->sort->attributes['woNo'] = [
            'asc' => ['trn_wo.no' => SORT_ASC],
            'desc' => ['trn_wo.no' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['scNo'] = [
            'asc' => ['trn_sc.no' => SORT_ASC],
            'desc' => ['trn_sc.no' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['marketingName'] = [
            'asc' => ['user.full_name' => SORT_ASC],
            'desc' => ['user.full_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['customerName'] = [
            'asc' => ['mst_customer.name' => SORT_ASC],
            'desc' => ['mst_customer.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['stock.date' => SORT_ASC],
            'desc' => ['stock.date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['header_id'] = [
            'asc' => ['trn_kirim_buyer.header_id' => SORT_ASC],
            'desc' => ['trn_kirim_buyer.header_id' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter date range
        if (!empty($this->dateRange)) {
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);
            if ($this->from_date === $this->to_date) {
                $query->andFilterWhere(['stock.date' => $this->from_date]);
            } else {
                $query->andFilterWhere(['between', 'stock.date', $this->from_date, $this->to_date]);
            }
        }

        $query->andFilterWhere([
            'trn_kirim_buyer_item.id' => $this->id,
            'trn_kirim_buyer_item.kirim_buyer_id' => $this->kirim_buyer_id,
            'trn_kirim_buyer_item.stock_id' => $this->stock_id,
            'trn_kirim_buyer_item.bal_id' => $this->bal_id,
            'trn_kirim_buyer_item.qty' => $this->qty,
            'trn_kirim_buyer.header_id' => $this->header_id,
        ]);

        $query->andFilterWhere(['ilike', 'trn_kirim_buyer_item.note', $this->note])
            ->andFilterWhere(['ilike', 'trn_kirim_buyer_item.no_bal', $this->no_bal])
            ->andFilterWhere(['ilike', 'trn_wo.no', $this->woNo])
            ->andFilterWhere(['ilike', 'trn_sc.no', $this->scNo])
            ->andFilterWhere(['ilike', 'user.full_name', $this->marketingName])
            ->andFilterWhere(['ilike', 'mst_customer.name', $this->customerName]);

            if ($this->noLot) {
                $query->andWhere([
                    'or',
                    ['in', 'source_ref', (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                        ->select('no')
                        ->where(['like', 'no_lot', $this->noLot])],
                    ['in', 'source_ref', (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                        ->select('no')
                        ->where(['like', 'no_lot', $this->noLot])]
                ]);
            }

        return $dataProvider;
    }
}