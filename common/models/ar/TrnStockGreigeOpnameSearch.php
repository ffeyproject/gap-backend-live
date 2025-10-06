<?php
namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TrnStockGreigeOpnameSearch extends TrnStockGreigeOpname
{
    public $dateRange;
    public $greigeNamaKain; // untuk filter nama_kain relasi

    public function rules()
    {
        return [
            [['id','stock_greige_id','greige_id','greige_group_id','asal_greige','grade','status_tsd','status','jenis_gudang'], 'integer'],
            [['date','dateRange','no_document','no_lapak','lot_lusi','lot_pakan','no_set_lusi','pengirim','mengetahui','note','greigeNamaKain'], 'safe'],
            [['panjang_m'], 'number'],
        ];
    }

    public function search($params)
    {
        // join relasi supaya bisa filter
        $query = TrnStockGreigeOpname::find()
            ->joinWith(['greige','greigeGroup']); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // filter biasa
        $query->andFilterWhere([
            'id' => $this->id,
            'stock_greige_id' => $this->stock_greige_id,
            'greige_id' => $this->greige_id,
            'greige_group_id' => $this->greige_group_id,
            'asal_greige' => $this->asal_greige,
            'grade' => $this->grade,
            'status_tsd' => $this->status_tsd,
            'status' => $this->status,
            'jenis_gudang' => $this->jenis_gudang,
            'panjang_m' => $this->panjang_m,
            'stock_greige_id' => $this->stock_greige_id,
            'lot_lusi' => $this->lot_lusi,
            'lot_pakan' => $this->lot_pakan,
        ]);

        // filter date range
        if(!empty($this->dateRange) && strpos($this->dateRange,' to ')!==false){
            list($start,$end)=explode(' to ',$this->dateRange);
            $query->andFilterWhere(['between','trn_stock_greige_opname.date',$start,$end]);
        }

        $query->andFilterWhere(['ilike','trn_stock_greige_opname.no_document',$this->no_document])
              ->andFilterWhere(['ilike','trn_stock_greige_opname.no_lapak',$this->no_lapak]);

        // filter nama kain relasi greige
        $query->andFilterWhere(['ilike','mst_greige.nama_kain',$this->greigeNamaKain]);

        return $dataProvider;
    }

    
}