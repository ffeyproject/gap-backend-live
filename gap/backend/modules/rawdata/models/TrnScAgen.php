<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_sc_agen".
 *
 * @property int $id
 * @property int $sc_id
 * @property string $date
 * @property string $nama_agen
 * @property string $attention
 * @property int|null $no_urut
 * @property string|null $no
 *
 * @property TrnSc $sc
 * @property TrnScKomisi[] $trnScKomisis
 */
class TrnScAgen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_agen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'date', 'nama_agen', 'attention'], 'required'],
            [['sc_id', 'no_urut'], 'default', 'value' => null],
            [['sc_id', 'no_urut'], 'integer'],
            [['date'], 'safe'],
            [['nama_agen', 'attention', 'no'], 'string', 'max' => 255],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'date' => 'Date',
            'nama_agen' => 'Nama Agen',
            'attention' => 'Attention',
            'no_urut' => 'No Urut',
            'no' => 'No',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScKomisis()
    {
        return $this->hasMany(TrnScKomisi::className(), ['sc_agen_id' => 'id']);
    }
}
