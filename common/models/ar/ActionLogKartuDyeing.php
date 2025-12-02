<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

class ActionLogKartuDyeing extends ActiveRecord
{
    public static function tableName()
    {
        return 'action_log_kartu_dyeing';
    }

    public function rules()
    {
        return [
            [['kartu_proses_id', 'action_name'], 'required'],
            [['description', 'user_agent'], 'string'],
            [['created_at'], 'safe'],
            [['user_id', 'kartu_proses_id'], 'integer'],
            [['username', 'action_name', 'ip'], 'string', 'max' => 255],
        ];
    }

    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesDyeing::class, ['id' => 'kartu_proses_id']);
    }

}