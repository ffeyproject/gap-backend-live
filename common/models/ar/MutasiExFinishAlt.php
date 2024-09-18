<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mutasi_ex_finish_alt".
 *
 * @property int $id
 * @property string $no_referensi
 * @property string $pemohon
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $no_urut
 * @property string|null $no
 *
 * @property MutasiExFinishAltItem[] $mutasiExFinishAltItems
 */
class MutasiExFinishAlt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mutasi_ex_finish_alt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_referensi', 'pemohon', 'created_at', 'created_by'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'default', 'value' => null],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['no_referensi', 'pemohon', 'no'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_referensi' => 'No Referensi',
            'pemohon' => 'Pemohon',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'no_urut' => 'No Urut',
            'no' => 'No',
        ];
    }

    /**
     * Gets query for [[MutasiExFinishAltItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMutasiExFinishAltItems()
    {
        return $this->hasMany(MutasiExFinishAltItem::className(), ['mutasi_id' => 'id']);
    }
}
