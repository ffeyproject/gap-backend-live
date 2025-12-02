<?php

namespace common\models\ar;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ActionLogKartuDyeingSearch extends ActionLogKartuDyeing
{
    public $woNo;
    public $warna;
    public $kartuNo;

    public function rules()
    {
        return [
            [['action_name', 'description', 'username', 'ip', 'woNo', 'warna', 'kartuNo'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = ActionLogKartuDyeing::find()
            ->joinWith(['kartuProses kp', 'kartuProses.wo wo', 'kartuProses.woColor.moColor mc'])
            ->orderBy(['wo.no' => SORT_ASC, 'mc.color' => SORT_ASC, 'kp.no' => SORT_ASC, 'action_log_kartu_dyeing.created_at' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['ilike', 'wo.no', $this->woNo])
              ->andFilterWhere(['ilike', 'mc.color', $this->warna])
              ->andFilterWhere(['ilike', 'kp.no', $this->kartuNo])
              ->andFilterWhere(['ilike', 'action_log_kartu_dyeing.action_name', $this->action_name])
              ->andFilterWhere(['ilike', 'action_log_kartu_dyeing.description', $this->description])
              ->andFilterWhere(['ilike', 'action_log_kartu_dyeing.username', $this->username]);

        return $dataProvider;
    }
}