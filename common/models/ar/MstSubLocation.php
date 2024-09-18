<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class MstSubLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_locs_sub';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'locs_add_date',
                'updatedAtAttribute' => 'locs_upd_date',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locs_code', 'locs_description', 'locs_active', 'locs_loc_id'], 'required'],
            [['locs_floor_code', 'locs_line_code', 'locs_column_code', 'locs_rack_code'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'locs_code' => 'Kode Sub Lokasi',
            'locs_floor_code' => 'Kode Lantai',
            'locs_line_code' => 'Kode Baris',
            'locs_column_code' => 'Kode Kolom',
            'locs_rack_code' => 'Kode Rak',
            'locs_description' => 'Deskripsi Sub Lokasi',
            'locs_active' => 'Aktif',
            'locs_loc_id' => 'Master Location',
            'locs_add_date' => 'Dibuat tanggal',
            'locs_add_by' => 'Dibuat oleh',
            'locs_upd_date' => 'Diperbarui tanggal',
            'locs_upd_by' => 'Diperbarui oleh'
        ];
    }

    /**
     * @return array
     */
    public static function optionList(){
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'locs_code', 'locs_code');
    }

}
