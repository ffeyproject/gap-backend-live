<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_hambatan_mesin".
 *
 * @property int $id
 * @property int $mst_mesin_proses_id
 * @property string $tanggal
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstMesinProses $mstMesinProses
 * @property TrnHambatanMesinItem[] $trnHambatanMesinItems
 * @property User $createdBy
 * @property User $updatedBy
 */
class TrnHambatanMesin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_hambatan_mesin';
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
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mst_mesin_proses_id', 'tanggal'], 'required'],
            [['mst_mesin_proses_id', 'created_by', 'updated_by'], 'integer'],
            [['tanggal', 'created_at', 'updated_at'], 'safe'],
            [['mst_mesin_proses_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstMesinProses::className(), 'targetAttribute' => ['mst_mesin_proses_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_mesin_proses_id' => 'Mesin',
            'tanggal' => 'Tanggal',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMstMesinProses()
    {
        return $this->hasOne(MstMesinProses::className(), ['id' => 'mst_mesin_proses_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnHambatanMesinItems()
    {
        return $this->hasMany(TrnHambatanMesinItem::className(), ['trn_hambatan_mesin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
