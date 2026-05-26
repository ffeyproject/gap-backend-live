<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_mesin_proses".
 *
 * @property int $id
 * @property string $nama_mesin
 * @property string|null $model_mesin
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MstJenisHambatan[] $mstJenisHambatans
 */
class MstMesinProses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_mesin_proses';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_mesin'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_mesin', 'model_mesin'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_mesin' => 'Nama/Nomor Mesin',
            'model_mesin' => 'Model Mesin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMstJenisHambatans()
    {
        return $this->hasMany(MstJenisHambatan::className(), ['id' => 'mst_jenis_hambatan_id'])
            ->viaTable('mst_mesin_proses_hambatan', ['mst_mesin_proses_id' => 'id']);
    }

    /**
     * Helper to get list of machines
     * @return array
     */
    public static function optionList()
    {
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'id', 'nama_mesin');
    }
}
