<?php

namespace backend\models\form;

use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\User;
use yii\base\Model;

/**
 * @property int $kartu_proses_id
 * @property string $note
*/
class CatatanProsesForm extends Model
{
    public $kartu_proses_id;
    public $note;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note', 'kartu_proses_id'], 'required'],
            ['kartu_proses_id', 'integer'],
            ['note', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'note' => 'Catatan Proses',
            'kartu_proses_id' => 'Kartu Proses Id'
        ];
    }
}