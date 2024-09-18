<?php

namespace common\models\ar;

use Romans\Filter\IntToRoman;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "trn_wo_memo".
 *
 * @property int $id
 * @property int $wo_id
 * @property int $tahun
 * @property int $no_urut
 * @property string $no
 * @property string $memo
 * @property int|null $created_at
 *
 * @property TrnWo $wo
 */
class TrnWoMemo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_wo_memo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wo_id', 'memo'], 'required'],
            [['wo_id', 'created_at'], 'default', 'value' => null],
            [['wo_id', 'created_at'], 'integer'],
            [['memo'], 'string'],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wo_id' => 'Wo ID',
            'tahun' => 'Tahun',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'memo' => 'Memo',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){
                $this->tahun = date('Y');
                $this->setNoUrut();
            }
            return true;
        }

        return false;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    private function setNoUrut(){
        //no urut/bulan romawi/tahun
        //direset tiap tahun

        $lastNoUrut = self::find()
            ->select('no_urut')
            ->where(['tahun'=>date("Y")])
            //->where(['>=', 'created_at', strtotime(date('Y'))])
            ->orderBy('id desc')
            ->scalar();
        if($lastNoUrut >= 0){
            $this->no_urut = $lastNoUrut + 1;
        }else{
            $this->no_urut = 1;
        }

        $mRoman = (new IntToRoman())->filter(date('m'));
        $this->no = $this->no_urut.'/'.$mRoman.'/'.date('Y');
    }
}
