<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "surat_jalan_ex_finish".
 *
 * @property int $id
 * @property int $memo_id
 * @property string|null $no
 * @property string|null $pengirim
 * @property string|null $penerima
 * @property string|null $kepala_gudang
 * @property string|null $plat_nomor
 * @property string|null $note
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 *
 * @property JualExFinish $memo
 */
class SuratJalanExFinish extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surat_jalan_ex_finish';
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
            [['memo_id', 'pengirim', 'penerima', 'kepala_gudang', 'plat_nomor'], 'required'],
            [['memo_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['memo_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['note'], 'string'],
            [['no', 'pengirim', 'penerima', 'kepala_gudang', 'plat_nomor'], 'string', 'max' => 255],
            //[['memo_id'], 'exist', 'skipOnError' => true, 'targetClass' => JualExFinish::className(), 'targetAttribute' => ['memo_id' => 'id']],
            ['memo_id', 'validateMemo'],
        ];
    }

    public function validateMemo($attribute, $params, $validator)
    {
        $memoExist = JualExFinish::find()->where(['id' => $this->$attribute])->count('id') > 0;
        if($memoExist === false){
            $this->addError($attribute, 'Memo penjualan tidak valid.');
        }

        $sudahDigunakan = SuratJalanExFinish::find()->where(['memo_id' => $this->$attribute])->count('id') > 0;
        if($sudahDigunakan){
            $this->addError($attribute, 'Memo penjualan tidak valid, sudah dibuat surat jalan nya.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($insert){
                $modelNo = new NomorSuratJalan([
                    'date' => date('Y-m-d'),
                    'created_at' => time()
                ]);
                $modelNo->setNomor();
                $modelNo->save(false);

                $this->no = $modelNo->no;
            }
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memo_id' => 'Memo ID',
            'no' => 'No',
            'pengirim' => 'Pengirim',
            'penerima' => 'Penerima',
            'kepala_gudang' => 'Kepala Gudang',
            'plat_nomor' => 'Plat Nomor',
            'note' => 'Note',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Memo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemo()
    {
        return $this->hasOne(JualExFinish::className(), ['id' => 'memo_id']);
    }
}
