<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_k3l".
 *
 * @property string $k3l_code
 * @property string|null $k3l_desc
 * @property string|null $k3l_active
 * @property int|null $k3l_add_by
 * @property string|null $k3l_add_date
 * @property int|null $k3l_upd_by
 * @property string|null $k3l_upd_date
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property InspectingMklBj[] $inspectingMklBjs
 * @property TrnInspecting[] $trnInspectings
 */
class MstK3l extends \yii\db\ActiveRecord
{
    const STATUS_AKTIF = 'Y';
    const STATUS_TIDAK_AKTIF = 'N';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_k3l';
    }

    /**
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_TIDAK_AKTIF => 'Tidak Aktif',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'k3l_add_date',
                'updatedAtAttribute' => 'k3l_upd_date',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'k3l_add_by',
                'updatedByAttribute' => 'k3l_upd_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['k3l_code'], 'required'],
            [['k3l_code'], 'trim'],
            [['k3l_code'], 'unique'],
            [['k3l_code'], 'string', 'max' => 45],
            [['k3l_desc'], 'trim'],
            [['k3l_desc'], 'string', 'max' => 175],
            [['k3l_active'], 'default', 'value' => self::STATUS_AKTIF],
            [['k3l_active'], 'in', 'range' => [self::STATUS_AKTIF, self::STATUS_TIDAK_AKTIF]],
            [['k3l_add_by', 'k3l_upd_by'], 'integer'],
            [['k3l_add_date', 'k3l_upd_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'k3l_code' => 'Kode K3L',
            'k3l_desc' => 'Deskripsi K3L',
            'k3l_active' => 'Status Aktif',
            'k3l_add_by' => 'Dibuat Oleh',
            'k3l_add_date' => 'Dibuat Pada',
            'k3l_upd_by' => 'Diubah Oleh',
            'k3l_upd_date' => 'Diubah Pada',
        ];
    }

    /**
     * @return array
     */
    public static function optionList($activeOnly = false)
    {
        $query = self::find();
        if ($activeOnly) {
            $query->where(['k3l_active' => self::STATUS_AKTIF]);
        }
        $models = $query->asArray()->all();
        return ArrayHelper::map($models, 'k3l_code', function($item) {
            return !empty($item['k3l_desc']) ? $item['k3l_desc'] : $item['k3l_code'];
        });
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'k3l_add_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'k3l_upd_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspectingMklBjs()
    {
        return $this->hasMany(InspectingMklBj::class, ['k3l_code' => 'k3l_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::class, ['k3l_code' => 'k3l_code']);
    }
}
