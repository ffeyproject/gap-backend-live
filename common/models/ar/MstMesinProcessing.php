<?php

namespace common\models\ar;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_mesin_processing".
 *
 * @property int $id
 * @property string $nama_mesin
 * @property string|null $relax_mesin
 * @property string|null $relax_jenis_nozzle
 * @property string|null $relax_ukuran_nozzle
 * @property string|null $relax_catatan
 * @property string|null $celup_mesin
 * @property string|null $celup_jenis_nozzle
 * @property string|null $celup_ukuran_nozzle
 * @property string|null $celup_catatan
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
            [['relax_catatan', 'celup_catatan'], 'string'],
            [['relax_mesin', 'relax_jenis_nozzle', 'relax_ukuran_nozzle', 'celup_mesin', 'celup_jenis_nozzle', 'celup_ukuran_nozzle'], 'string', 'max' => 255],
            [['nama_mesin'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if (is_array($this->nama_mesin)) {
            $this->nama_mesin = implode(', ', $this->nama_mesin);
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->nama_mesin)) {
            $this->nama_mesin = array_map('trim', explode(',', $this->nama_mesin));
        } else {
            $this->nama_mesin = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_mesin' => 'Nama Motif',
            'relax_mesin' => 'Mesin',
            'relax_jenis_nozzle' => 'Jenis Nozzle',
            'relax_ukuran_nozzle' => 'Ukuran Nozzle',
            'relax_catatan' => 'Catatan',
            'celup_mesin' => 'Mesin',
            'celup_jenis_nozzle' => 'Jenis Nozzle',
            'celup_ukuran_nozzle' => 'Ukuran Nozzle',
            'celup_catatan' => 'Catatan',
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
