<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_mixed_greige".
 *
 * @property int $id
 * @property int $greige_id di mix menjadi greige ini
 * @property int $status 1=draft, 2=posted
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstGreige $greige
 * @property TrnMixedGreigeItem[] $trnMixedGreigeItems
 */
class TrnMixedGreige extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mixed_greige';
    }

    /**
     * {@inheritdoc}
     */
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
            [['greige_id'], 'required'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED]],
            [['greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_id' => 'Greige ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[TrnMixedGreigeItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMixedGreigeItems()
    {
        return $this->hasMany(TrnMixedGreigeItem::className(), ['mix_id' => 'id']);
    }
}
