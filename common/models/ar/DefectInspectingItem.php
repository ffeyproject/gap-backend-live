<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "defect_inspecting_items".
 *
 * @property int $id
 * @property int $inspecting_item_id
 * @property int $mst_kode_defect_id
 * @property float $meterage
 * @property float $point
 * @property string $created_at
 * @property string $updated_at
 *
 * @property InspectingItem $inspectingItem
 * @property MstKodeDefect $mstKodeDefect
 * @property InspectingMklBjItems $inspectingMklBjItems 
 */
class DefectInspectingItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'defect_inspecting_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspecting_item_id', 'inspecting_mklbj_item_id', 'mst_kode_defect_id', 'meterage', 'point'], 'required'],
            [['inspecting_item_id', 'inspecting_mklbj_item_id', 'mst_kode_defect_id'], 'integer'],
            [['meterage', 'point'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['inspecting_item_id', 'inspecting_mklbj_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InspectingItem::className(), 'targetAttribute' => ['inspecting_item_id' => 'id']],
            [['mst_kode_defect_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstKodeDefect::className(), 'targetAttribute' => ['mst_kode_defect_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inspecting_item_id' => 'Inspecting Item ID',
            'inspecting_mklbj_item_id' => 'Inspecting Mklbj Item ID',
            'mst_kode_defect_id' => 'Mst Kode Defect ID',
            'meterage' => 'Meterage',
            'point' => 'Point',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InspectingItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectingItem()
    {
        return $this->hasOne(InspectingItem::className(), ['id' => 'inspecting_item_id']);
    }

    public function getInspectingMklbjItem() 
    {
        return $this->hasOne(InspectingMklBjItems::className(), ['id' => 'inspecting_mklbj_item_id']);
    }

    /**
     * Gets query for [[MstKodeDefect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMstKodeDefect()
    {
        return $this->hasOne(MstKodeDefect::className(), ['id' => 'mst_kode_defect_id']);
    }

    
}