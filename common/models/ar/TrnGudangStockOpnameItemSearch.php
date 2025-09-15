<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\TrnGudangStockOpnameItem;

class TrnGudangStockOpnameItemSearch extends TrnGudangStockOpnameItem
{
    // tambahkan properti untuk filter relasi
    public $no_document;
    public $operator;
    public $date;
    public $jenis_gudang;
    public $status_tsd;
    public $no_lapak;
    private $from_date;
    private $to_date;
    public $dateRange;
    public $greigeNamaKain;
    public $lot_lusi;
    public $lot_pakan;
    public $status;
    public $asal_greige;
    public $statusGudangStockOpname;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trn_gudang_stock_opname_id'], 'integer'],
            [['is_out'], 'boolean'],
            [['panjang_m'], 'number'],
            [['no_set_lusi', 'grade', 'ket_defect', 'created_at', 'updated_at'], 'safe'],

            // relasi
            [['no_document', 'operator', 'date','no_lapak', 'dateRange','greigeNamaKain', 'lot_lusi', 'lot_pakan'], 'safe'],
            [['jenis_gudang','status','status_tsd','asal_greige'], 'integer'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return [
            'no_document' => 'No Document',
            'no_lapak' => 'No Lapak',
            'greigeNamaKain' => 'Nama Kain',
            'no_set_lusi' => 'No. MC Weaving',
            'is_out' => 'Keluar',
        ];
    }

    public function search($params)
    {
        $query = TrnGudangStockOpnameItem::find();

        // join ke relasi
        $query->joinWith(['trnGudangStockOpname tso']);
        $query->joinWith(['trnGudangStockOpname.greige greige']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // supaya sorting bisa juga berdasarkan kolom relasi
        $dataProvider->sort->attributes['no_document'] = [
            'asc' => ['tso.no_document' => SORT_ASC],
            'desc' => ['tso.no_document' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['operator'] = [
            'asc' => ['tso.operator' => SORT_ASC],
            'desc' => ['tso.operator' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['date'] = [
            'asc' => ['tso.date' => SORT_ASC],
            'desc' => ['tso.date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['jenis_gudang'] = [
            'asc' => ['tso.jenis_gudang' => SORT_ASC],
            'desc' => ['tso.jenis_gudang' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['no_lapak'] = [
            'asc' => ['tso.no_lapak' => SORT_ASC],
            'desc' => ['tso.no_lapak' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => ['tso.date' => SORT_ASC],
            'desc' => ['tso.date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['greige.nama_kain' => SORT_ASC],
            'desc' => ['greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lot_lusi'] = [
            'asc' => ['tso.lot_lusi' => SORT_ASC],
            'desc' => ['tso.lot_lusi' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lot_pakan'] = [
            'asc' => ['tso.lot_pakan' => SORT_ASC],
            'desc' => ['tso.lot_pakan' => SORT_DESC],
        ];

        

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1'); // tidak tampilkan hasil kalau filter tidak valid
            return $dataProvider;
        }

        if(!empty($this->dateRange)){
            $this->from_date = substr($this->dateRange, 0, 10);
            $this->to_date = substr($this->dateRange, 14);

            if($this->from_date == $this->to_date){
                $query->andFilterWhere(['tso.date' => $this->from_date]);
            }else{
                $query->andFilterWhere(['between', 'tso.date', $this->from_date, $this->to_date]);
            }
        }
        if ($this->statusGudangStockOpname !== null && $this->statusGudangStockOpname !== '') {
            $query->andWhere(['tso.status' => $this->statusGudangStockOpname]);
        }

        // filter di tabel utama (Item)
        $query->andFilterWhere([
            'id' => $this->id,
            'trn_gudang_stock_opname_id' => $this->trn_gudang_stock_opname_id,
            'panjang_m' => $this->panjang_m,
        ]);

        $query->andFilterWhere(['like', 'no_set_lusi', $this->no_set_lusi])
              ->andFilterWhere(['like', 'grade', $this->grade])
              ->andFilterWhere(['is_out' => $this->is_out])
              ->andFilterWhere(['like', 'ket_defect', $this->ket_defect]);

        // filter dari relasi
        $query->andFilterWhere(['like', 'tso.no_document', $this->no_document])
              ->andFilterWhere(['like', 'tso.operator', $this->operator])
              ->andFilterWhere(['tso.jenis_gudang' => $this->jenis_gudang])
              ->andFilterWhere(['date(tso.date)' => $this->date])
              ->andFilterWhere(['tso.status_tsd' => $this->status_tsd])
              ->andFilterWhere(['like','tso.no_lapak', $this->no_lapak])
              ->andFilterWhere(['like','greige.nama_kain', $this->greigeNamaKain])
              ->andFilterWhere(['like', 'tso.lot_lusi', $this->lot_lusi])
              ->andFilterWhere(['like', 'tso.lot_pakan', $this->lot_pakan])
              ->andFilterWhere(['tso.status' => $this->status])
              ->andFilterWhere(['tso.asal_greige' => $this->asal_greige]);


        return $dataProvider;
    }
}
