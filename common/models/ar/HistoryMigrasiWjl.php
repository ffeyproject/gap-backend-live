<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "history_migrasi_wjl".
 *
 * @property int $id
 * @property int $greige_id
 * @property float|null $total_qty_out
 * @property int|null $jumlah_roll_out
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property MstGreige $greige
 */
class HistoryMigrasiWjl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history_migrasi_wjl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_id'], 'required'],
            [['greige_id', 'jumlah_roll_out', 'created_at', 'created_by'], 'default', 'value' => null],
            [['greige_id', 'jumlah_roll_out', 'created_at', 'created_by'], 'integer'],
            [['total_qty_out'], 'number'],
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
            'total_qty_out' => 'Total Qty Out',
            'jumlah_roll_out' => 'Jumlah Roll Out',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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
}
