<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * TrnStockGreigeDailySearch represents the model behind the search form of `common\models\ar\TrnStockGreigeDaily`.
 */
class TrnStockGreigeDailySearch extends TrnStockGreigeDaily
{
    public $dateRange;
    public $fromDate;
    public $toDate;
    public $greigeNamaKain;
    public $diffStatus; // 'all', 'positive' (bertambah), 'negative' (berkurang), 'zero' (tetap)

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'greige_id', 'greige_group_id', 'asal_greige', 'total_roll', 'created_by', 'updated_by'], 'integer'],
            [['date', 'dateRange', 'fromDate', 'toDate', 'greigeNamaKain', 'diffStatus', 'note'], 'safe'],
            [['total_panjang', 'diff_panjang', 'prev_total_panjang'], 'number'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $lateralSubquery = '(
            SELECT p.total_panjang, p.total_roll, p.date
            FROM trn_stock_greige_daily p
            WHERE p.greige_id = tsgd.greige_id 
              AND p.asal_greige = tsgd.asal_greige 
              AND p.date < tsgd.date
            ORDER BY p.date DESC
            LIMIT 1
        )';

        $query = TrnStockGreigeDaily::find()
            ->alias('tsgd')
            ->select([
                'tsgd.*',
                'prev_total_panjang' => new Expression('COALESCE(prev.total_panjang, 0)'),
                'prev_total_roll' => new Expression('COALESCE(prev.total_roll, 0)'),
                'prev_date' => new Expression('prev.date'),
                'diff_panjang' => new Expression('(tsgd.total_panjang - COALESCE(prev.total_panjang, 0))'),
                'diff_roll' => new Expression('(tsgd.total_roll - COALESCE(prev.total_roll, 0))'),
            ])
            ->join('LEFT JOIN LATERAL', "{$lateralSubquery} prev", 'true')
            ->joinWith(['greige', 'greigeGroup']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'greigeNamaKain' => SORT_ASC,
                ],
                'attributes' => [
                    'id',
                    'date',
                    'greige_id',
                    'asal_greige',
                    'greigeNamaKain' => [
                        'asc' => [
                            new Expression("CASE WHEN mst_greige.nama_kain ~* '^[a-z]' THEN 0 ELSE 1 END ASC"),
                            'mst_greige.nama_kain' => SORT_ASC,
                        ],
                        'desc' => [
                            new Expression("CASE WHEN mst_greige.nama_kain ~* '^[a-z]' THEN 0 ELSE 1 END ASC"),
                            'mst_greige.nama_kain' => SORT_DESC,
                        ],
                    ],
                    'total_panjang',
                    'total_roll',
                    'grade_a',
                    'grade_b',
                    'grade_c',
                    'grade_d',
                    'grade_e',
                    'grade_ng',
                    'grade_lain',
                    'prev_total_panjang',
                    'diff_panjang',
                    'diff_roll',
                    'created_at',
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter date range
        if (!empty($this->dateRange)) {
            $dates = explode(' to ', $this->dateRange);
            if (count($dates) === 2) {
                $query->andFilterWhere(['between', 'tsgd.date', trim($dates[0]), trim($dates[1])]);
            } else {
                $query->andFilterWhere(['tsgd.date' => trim($dates[0])]);
            }
        } elseif (!empty($this->fromDate) && !empty($this->toDate)) {
            $query->andFilterWhere(['between', 'tsgd.date', $this->fromDate, $this->toDate]);
        } elseif (!empty($this->fromDate)) {
            $query->andFilterWhere(['>=', 'tsgd.date', $this->fromDate]);
        } elseif (!empty($this->toDate)) {
            $query->andFilterWhere(['<=', 'tsgd.date', $this->toDate]);
        }

        $query->andFilterWhere([
            'tsgd.id' => $this->id,
            'tsgd.date' => $this->date,
            'tsgd.greige_id' => $this->greige_id,
            'tsgd.greige_group_id' => $this->greige_group_id,
            'tsgd.asal_greige' => $this->asal_greige,
            'tsgd.total_roll' => $this->total_roll,
        ]);

        $query->andFilterWhere(['ilike', 'mst_greige.nama_kain', $this->greigeNamaKain]);
        $query->andFilterWhere(['ilike', 'tsgd.note', $this->note]);

        // Filter status perubahan
        if ($this->diffStatus === 'positive') {
            $query->andWhere(new Expression('(tsgd.total_panjang - COALESCE(prev.total_panjang, 0)) > 0'));
        } elseif ($this->diffStatus === 'negative') {
            $query->andWhere(new Expression('(tsgd.total_panjang - COALESCE(prev.total_panjang, 0)) < 0'));
        } elseif ($this->diffStatus === 'zero') {
            $query->andWhere(new Expression('(tsgd.total_panjang - COALESCE(prev.total_panjang, 0)) = 0'));
        } elseif ($this->diffStatus === 'changed') {
            $query->andWhere(new Expression('(tsgd.total_panjang - COALESCE(prev.total_panjang, 0)) != 0'));
        }

        return $dataProvider;
    }

    /**
     * Option list status perubahan
     * @return array
     */
    public static function diffStatusOptions()
    {
        return [
            'positive' => 'Bertambah (+)',
            'negative' => 'Berkurang (-)',
            'zero' => 'Tetap (0)',
            'changed' => 'Berubah (+/-)',
        ];
    }
}
