<?php

namespace common\models\ar;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_mesin_processing".
 *
 * @property int $id
 * @property string $nama_mesin
 * @property string|null $jenis_mesin
 * @property string|null $jenis_nozzle
 * @property string|null $ukuran_nozzle
 */
class MstMesinProcessing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_mesin_processing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_mesin'], 'required'],
            [['nama_mesin', 'jenis_mesin', 'jenis_nozzle', 'ukuran_nozzle'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_mesin' => 'Nama Mesin',
            'jenis_mesin' => 'Jenis Mesin',
            'jenis_nozzle' => 'Jenis Nozzle',
            'ukuran_nozzle' => 'Ukuran Nozzle',
        ];
    }

    /**
     * @return array
     */
    public static function optionList()
    {
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'id', 'nama_mesin');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForbiddenGreiges()
    {
        return $this->hasMany(MstMesinProcessingForbiddenGreige::className(), ['mesin_id' => 'id']);
    }
}
