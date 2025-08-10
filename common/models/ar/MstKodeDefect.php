<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "mst_kode_defect".
 *
 * @property int $id
 * @property int $no_urut
 * @property string $kode
 * @property string $nama_defect
 * @property string $asal_defect
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DefectInspectingItem[] $defectInspectingItems
 */
class MstKodeDefect extends \yii\db\ActiveRecord
{
    const ASAL_DEFECT_PR      = 'PR';
    const ASAL_DEFECT_PC      = 'PC';
    const ASAL_DEFECT_MKL     = 'MKL';
    const ASAL_DEFECT_DIGITAL = 'DIGITAL';
    const ASAL_DEFECT_WEAVING = 'W';

    public static $asalDefectList = [
        self::ASAL_DEFECT_PR      => 'PR',
        self::ASAL_DEFECT_PC      => 'PC',
        self::ASAL_DEFECT_MKL     => 'MKL',
        self::ASAL_DEFECT_DIGITAL => 'DIGITAL',
        self::ASAL_DEFECT_WEAVING => 'W',
    ];

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_kode_defect';
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
            [['no_urut', 'kode', 'nama_defect', 'asal_defect'], 'required'],
            [['no_urut'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode', 'nama_defect', 'asal_defect'], 'string', 'max' => 255],
            ['asal_defect', 'in', 'range' => array_keys(self::$asalDefectList)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_urut' => 'No Urut',
            'kode' => 'Kode',
            'nama_defect' => 'Nama Defect',
            'asal_defect' => 'Asal Defect',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefectInspectingItems()
    {
        return $this->hasMany(DefectInspectingItem::className(), ['mst_kode_defect_id' => 'id']);
    }
}