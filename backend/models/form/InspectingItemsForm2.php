<?php
namespace backend\models\form;

use common\models\ar\InspectingItem;
use yii\base\Model;

/**
 *
 */
class InspectingItemsForm extends Model
{
    public $grade;
    public $ukuran;
    public $join_piece;
    public $keterangan;

    /**
     * @inheritDoc
    */
    public function rules()
    {
        return [
            [['grade', 'ukuran'], 'required'],
            ['ukuran', 'number'],
            ['join_piece', 'match', 'pattern' => '/^[A-Z]{1,2}$/'],
            [['join_piece', 'keterangan'], 'string'],
            ['grade', 'in', 'range' => [InspectingItem::GRADE_A, InspectingItem::GRADE_B, InspectingItem::GRADE_C, InspectingItem::GRADE_PK, InspectingItem::GRADE_SAMPLE, InspectingItem::GRADE_A_PLUS, InspectingItem::GRADE_A_ASTERISK]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'grade' => 'Grade',
            'ukuran' => 'Ukuran',
            'join_piece' => 'Join Piece',
            'keterangan' => 'Keterangan'
        ];
    }


}