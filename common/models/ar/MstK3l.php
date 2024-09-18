<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_k3l".
 *
 * @property string $k3l_code
 * @property boolean $k3l_active
 * @property string $k3l_desc
 *
 * @property TrnWo[] $trnWos
 */
class MstK3l extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_k3l';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['k3l_code', 'k3l_active'], 'required'],
            [['k3l_desc', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['k3l_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'k3l_code' => 'Kode K3L',
            'k3l_desc' => 'Deskripsi K3L',
            'nama_kain' => 'Nama Kain',
            'k3l_active' => 'Is_active'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){
                //jumlah available harus sama dengan jumlah stock
                $this->available = $this->stock;
            }

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public static function optionList(){
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'k3l_code', 'k3l_desc');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getGroup()
    // {
    //     return $this->hasOne(MstGreigeGroup::className(), ['id' => 'group_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getTrnStockGreiges()
    // {
    //     return $this->hasMany(TrnStockGreige::className(), ['greige_id' => 'id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getTrnWos()
    // {
    //     return $this->hasMany(TrnWo::className(), ['greige_id' => 'id']);
    // }

    /**
     * @return float
     * tidak dipakai lagi, sudah digantikan oleh kolom tambahan "available"
     */
    /*public function getAvailableStock()
    {
        return (float)$this->stock - (float)$this->booked;
    }*/
}
