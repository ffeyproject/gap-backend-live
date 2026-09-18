<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "wms_move_location_dtl".
 *
 * @property int $moved_id
 * @property string $moved_move_code
 * @property int $moved_id_stok
 *
 * @property WmsMoveLocationMstr $moveLocationMstr
 */
class WmsMoveLocationDtl extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_move_location_dtl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['moved_move_code', 'moved_id_stok'], 'required'],
            [['moved_id_stok'], 'default', 'value' => null],
            [['moved_id_stok'], 'integer'],
            [['moved_move_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'moved_id' => 'Moved ID',
            'moved_move_code' => 'Moved Move Code',
            'moved_id_stok' => 'Moved Id Stok',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoveLocationMstr()
    {
        return $this->hasOne(WmsMoveLocationMstr::className(), ['move_code' => 'moved_move_code']);
    }
}
