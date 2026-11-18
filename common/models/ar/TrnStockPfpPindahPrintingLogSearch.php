<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TrnStockPfpPindahPrintingLogSearch represents the model behind the search form of `common\models\ar\TrnStockPfpPindahPrintingLog`.
 */
class TrnStockPfpPindahPrintingLogSearch extends TrnStockPfpPindahPrintingLog
{
    public $greigeNamaKain;
    public $createdByName;
    public $dateRange;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stock_id', 'greige_id', 'jenis_printing_asal', 'jenis_printing_tujuan', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['panjang_m'], 'number'],
            [['note', 'greigeNamaKain', 'createdByName', 'dateRange'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = TrnStockPfpPindahPrintingLog::find()->joinWith(['greige', 'createdBy']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['greigeNamaKain'] = [
            'asc' => ['mst_greige.nama_kain' => SORT_ASC],
            'desc' => ['mst_greige.nama_kain' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['createdByName'] = [
            'asc' => ['user.full_name' => SORT_ASC],
            'desc' => ['user.full_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->dateRange)) {
            $this->from_date = strtotime(substr($this->dateRange, 0, 10) . ' 00:00:00');
            $this->to_date = strtotime(substr($this->dateRange, 14) . ' 23:59:59');

            $query->andFilterWhere(['between', 'trn_stock_pfp_pindah_printing_log.created_at', $this->from_date, $this->to_date]);
        }

        $query->andFilterWhere([
            'trn_stock_pfp_pindah_printing_log.id' => $this->id,
            'trn_stock_pfp_pindah_printing_log.stock_id' => $this->stock_id,
            'trn_stock_pfp_pindah_printing_log.greige_id' => $this->greige_id,
            'trn_stock_pfp_pindah_printing_log.panjang_m' => $this->panjang_m,
            'trn_stock_pfp_pindah_printing_log.jenis_printing_asal' => $this->jenis_printing_asal,
            'trn_stock_pfp_pindah_printing_log.jenis_printing_tujuan' => $this->jenis_printing_tujuan,
            'trn_stock_pfp_pindah_printing_log.created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['ilike', 'trn_stock_pfp_pindah_printing_log.note', $this->note])
            ->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain])
            ->andFilterWhere(['ilike', 'user.full_name', $this->createdByName]);

        return $dataProvider;
    }
}
