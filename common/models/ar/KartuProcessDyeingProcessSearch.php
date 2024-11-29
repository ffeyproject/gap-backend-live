<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ar\KartuProcessDyeingProcess;
use yii\db\Expression;

/**
 * KartuProcessDyeingProcessSearch represents the model behind the search form of `common\models\ar\KartuProcessDyeingProcess`.
 */
class KartuProcessDyeingProcessSearch extends KartuProcessDyeingProcess
{   
    public $dateRange;
    public $nama_proses;
    public $warna;
    public $woNo;
    public $no_kartu;
    public $no_mesin;
    public $shift_group;
    private $from_date;
    private $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kartu_process_id', 'process_id', 'value'], 'integer'],
            [['warna','woNo','no_kartu','no_mesin','shift_group'], 'string'],
            [['note','dateRange','nama_proses'], 'safe'],
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

        $query = KartuProcessDyeingProcess::find()
        ->joinWith(['kartuProcess','process','kartuProcess.woColor.moColor as mo_color','kartuProcess.wo as wo'])
        ->andWhere(new \yii\db\Expression("CAST(value AS jsonb)->>'tanggal' IS NOT NULL"));    

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'tanggal' => SORT_DESC,
                ],
                'attributes' => [
                    'tanggal' => [
                        'asc' => [new Expression("CAST(value AS jsonb)->>'tanggal' ASC")],
                        'desc' => [new Expression("CAST(value AS jsonb)->>'tanggal' DESC")],
                    ],
                ],
            ],
        ]);

        $dataProvider->sort->attributes['dateRange'] = [
            'asc' => [new Expression("CAST(value AS jsonb)->>'tanggal' ASC")],
            'desc' => [new Expression("CAST(value AS jsonb)->>'tanggal' DESC")],
        ];

        $this->load($params);


        if (!empty($this->dateRange)) {
            $dataProvider->pagination = false;
            $this->from_date = substr($this->dateRange, 0, 10); // Tanggal awal
            $this->to_date = substr($this->dateRange, 14); // Tanggal akhir
        
            if ($this->from_date == $this->to_date) {
                // Menambahkan validasi untuk memastikan ada tanggal yang valid di dalam JSON
                $query->andFilterWhere([
                    'and',
                    [
                        '=',
                        new \yii\db\Expression("COALESCE(value::jsonb->>'tanggal', '')"),
                        $this->from_date
                    ],
                    [
                        'not',
                        new \yii\db\Expression("COALESCE(value::jsonb->>'tanggal', '') = ''")
                    ]
                ]);
            } else {
                // Menambahkan validasi untuk memastikan ada tanggal yang valid di dalam JSON
                $query->andFilterWhere([
                    'and',
                    [
                        'between',
                        new \yii\db\Expression("COALESCE(value::jsonb->>'tanggal', '')"),
                        $this->from_date,
                        $this->to_date
                    ],
                    [
                        'not',
                        new \yii\db\Expression("COALESCE(value::jsonb->>'tanggal', '') = ''")
                    ]
                ]);
            }
        }
        
        $query->andFilterWhere(['mst_process_dyeing.id' => $this->nama_proses]);

        $query->andFilterWhere(['mo_color.color' => $this->warna]);

        $query->andFilterWhere(['wo.no' => $this->woNo]);

        $query->andFilterWhere(['like', 'trn_kartu_proses_dyeing.no', $this->no_kartu]);

        $query->andFilterWhere([
            'ilike',
            new \yii\db\Expression("COALESCE(value::jsonb->>'no_mesin', '')"),
            $this->no_mesin
        ]);

        $query->andFilterWhere([
            'ilike',
            new \yii\db\Expression("COALESCE(value::jsonb->>'shift_group', '')"),
            $this->shift_group
        ]);
        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'kartu_process_id' => $this->kartu_process_id,
            'process_id' => $this->process_id,
            'value' => $this->value,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
