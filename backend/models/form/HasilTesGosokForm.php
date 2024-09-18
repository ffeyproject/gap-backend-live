<?php

namespace backend\models\form;

use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\User;
use yii\base\Model;

/**
 * @property int kartu_proses_id
 * @property string hasil_tes_gosok
*/
class HasilTesGosokForm extends Model
{
    public $kartu_proses_id;
    public $hasil_tes_gosok;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hasil_tes_gosok', 'kartu_proses_id'], 'required'],
            ['kartu_proses_id', 'integer'],
            ['hasil_tes_gosok', 'string'],
            //[['kartu_proses_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKartuProsesDyeing::className(), 'targetAttribute' => ['kartu_proses_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasil_tes_gosok' => 'Hasil Tes Gosok',
            'kartu_proses_id' => 'Kartu Proses Id'
        ];
    }

    /**
    */
    public function save(){
        if ($this->validate()){
            $model = TrnKartuProsesDyeing::findOne($this->kartu_proses_id);
            if($model === null){
                $this->addError('kartu_proses_id', 'Kartu Proses Id tidak valid');
                return false;
            }

            $model->hasil_tes_gosok = $this->hasil_tes_gosok;
            $model->save(false, ['hasil_tes_gosok']);
            return true;
        }

        return false;
    }
}