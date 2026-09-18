<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "wms_move_location_mstr".
 *
 * @property string $move_code
 * @property string|null $move_date
 * @property string|null $move_create_at
 * @property int|null $move_create_by
 * @property int|null $move_count
 * @property string|null $move_locs_code_from
 * @property string|null $move_locs_code_to
 *
 * @property WmsMoveLocationDtl[] $details
 */
class WmsMoveLocationMstr extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_move_location_mstr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['move_code'], 'required'],
            [['move_date', 'move_create_at'], 'safe'],
            [['move_create_by', 'move_count'], 'default', 'value' => null],
            [['move_create_by', 'move_count'], 'integer'],
            [['move_code', 'move_locs_code_from', 'move_locs_code_to'], 'string', 'max' => 255],
            [['move_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'move_code' => 'Move Code',
            'move_date' => 'Move Date',
            'move_create_at' => 'Created At',
            'move_create_by' => 'Created By',
            'move_count' => 'Move Count',
            'move_locs_code_from' => 'From Location',
            'move_locs_code_to' => 'To Location',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetails()
    {
        return $this->hasMany(WmsMoveLocationDtl::className(), ['moved_move_code' => 'move_code']);
    }

    /**
     * Generate move_code format ML-YYYYMMxxxxx
     * @return string
     */
    public static function generateMoveCode()
    {
        $prefix = 'ML-' . date('Ym');
        $latest = self::find()
            ->where(['like', 'move_code', $prefix . '%', false])
            ->orderBy(['move_code' => SORT_DESC])
            ->one();

        if ($latest) {
            $lastNum = (int) substr($latest->move_code, -5);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }
}
