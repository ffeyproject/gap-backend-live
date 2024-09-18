<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "mst_handling".
 *
 * @property int $id
 * @property int|null $greige_id
 * @property string $name
 * @property string $lebar_preset
 * @property string $lebar_finish
 * @property string $berat_finish
 * @property string $densiti_lusi
 * @property string $densiti_pakan
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstGreige $greige
 * @property TrnMo[] $trnMos
 */
class MstHandling extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_handling';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'lebar_preset', 'lebar_finish', 'berat_finish', 'densiti_lusi', 'densiti_pakan'], 'required'],
            [['name', 'lebar_preset', 'lebar_finish', 'berat_finish', 'densiti_lusi', 'densiti_pakan'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            'densiti_lusi' => 'Densiti Lusi',
            'densiti_pakan' => 'Densiti Pakan',
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
     * Gets query for [[TrnMos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['handling_id' => 'id']);
    }
}
