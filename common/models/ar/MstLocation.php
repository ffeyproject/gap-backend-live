<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class MstLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_loc_mstr';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'loc_add_date',
                'updatedAtAttribute' => 'loc_upd_date',
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
            [['loc_id', 'loc_name', 'loc_description', 'loc_active'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'loc_id' => 'ID Lokasi',
            'loc_name' => 'Nama Lokasi',
            'loc_description' => 'Deskripsi Lokasi',
            'loc_active' => 'Aktif',
            'loc_add_date' => 'Dibuat tanggal',
            'loc_add_by' => 'Dibuat oleh',
            'loc_upd_date' => 'Diperbarui tanggal',
            'loc_upd_by' => 'Diperbarui oleh',
        ];
    }

        /**
     * @return array
     */
    public static function optionList(){
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'loc_id', 'loc_name');
    }

}
