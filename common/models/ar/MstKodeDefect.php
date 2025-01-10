<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "mst_kode_defect".
 *
 * @property int $id
 * @property int $no_urut
 * @property string $kode
 * @property string $nama_defect
 * @property string $asal_defect
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DefectInspectingItem[] $defectInspectingItems
 */
class MstKodeDefect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_kode_defect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_urut', 'kode', 'nama_defect', 'asal_defect'], 'required'],
            [['no_urut'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode', 'nama_defect', 'asal_defect'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_urut' => 'No Urut',
            'kode' => 'Kode',
            'nama_defect' => 'Nama Defect',
            'asal_defect' => 'Asal Defect',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefectInspectingItems()
    {
        return $this->hasMany(DefectInspectingItem::className(), ['mst_kode_defect_id' => 'id']);
    }
}