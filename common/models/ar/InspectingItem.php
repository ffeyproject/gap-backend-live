<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "inspecting_item".
 *
 * @property int $id
 * @property int $inspecting_id
 * @property int $grade 1=Grade A, 2=Grade B, 3=Grade C, 4=Piece Kecil, 5=Sample, 7=Grade A+, 8=Grade A*, 9=Putih, 10=Grade D
 * @property string|null $join_piece
 * @property float $qty
 * @property string|null $note
 *
 * @property TrnInspecting $inspecting
 */
class InspectingItem extends \yii\db\ActiveRecord
{
    const GRADE_A = 1; const GRADE_B = 2; const GRADE_C = 3; const GRADE_PK = 4; const GRADE_SAMPLE = 5; const GRADE_A_PLUS = 7; const GRADE_A_ASTERISK = 8; const GRADE_PUTIH = 9; const GRADE_D = 10;
    /**
     * @return array
     */
    public static function gradeOptions(){
        return [
            self::GRADE_A => 'Grade A',
            self::GRADE_B => 'Grade B',
            self::GRADE_A_PLUS => 'Grade A+',
            self::GRADE_A_ASTERISK => 'Grade A*',
            self::GRADE_C => 'Grade C',
            self::GRADE_PK => 'Piece Kecil',
            self::GRADE_D => 'Grade D', 
            self::GRADE_SAMPLE => 'Sample',
            self::GRADE_PUTIH => 'Putih',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspecting_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspecting_id', 'grade', 'qty'], 'required'],
            [['inspecting_id', 'grade', 'join_piece'], 'default', 'value' => null],
            [['inspecting_id', 'grade', 'no_urut'], 'integer'],
            [['no_urut'], 'default', 'value' => null],
            [['qty'], 'number'],
            [['note'], 'string'],
            [['join_piece'], 'string', 'max' => 10],
            [['inspecting_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnInspecting::className(), 'targetAttribute' => ['inspecting_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inspecting_id' => 'Inspecting ID',
            'grade' => 'Grade',
            'join_piece' => 'Join Piece',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Inspecting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspecting()
    {
        return $this->hasOne(TrnInspecting::className(), ['id' => 'inspecting_id']);
    }

    
    /**
     * Gets query for [[DefectInspectingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDefectInspectingItems()
    {
        return $this->hasMany(DefectInspectingItem::className(), ['inspecting_item_id' => 'id']);
    }



    /**
     * Menghitung total point berdasarkan inspecting_id dan join_piece yang sama.
     *
     * @param int $inspectingId
     * @param string $joinPiece
     * @return float
     */
    public function getTotalPoints($inspectingId, $joinPiece)
    {
        $totalPoint = DefectInspectingItem::find()
            ->joinWith('inspectingItem')
            ->where(['inspecting_item.inspecting_id' => $inspectingId, 'inspecting_item.join_piece' => $joinPiece])
            ->sum('defect_inspecting_items.point');

        return $totalPoint;
    }

}