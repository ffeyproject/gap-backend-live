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
    public $defect;
    public $lot_no;
    public $ukuran;
    public $join_piece;
    public $keterangan;
    public $no_urut;

    /**
     * @inheritDoc
    */
    public function rules()
    {
        return [
            [['grade', 'ukuran'], 'required'],
            [['defect', 'lot_no'], 'safe'],
            ['ukuran', 'number'],
            ['no_urut', 'integer'],
            ['join_piece', 'match', 'pattern' => '/^[A-Z]{1,2}$/'],
            [['join_piece', 'keterangan'], 'string'],
            ['grade', 'in', 'range' => [InspectingItem::GRADE_A, InspectingItem::GRADE_B, InspectingItem::GRADE_C, InspectingItem::GRADE_PK, InspectingItem::GRADE_SAMPLE, InspectingItem::GRADE_A_PLUS, InspectingItem::GRADE_A_ASTERISK, InspectingItem::GRADE_PUTIH]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'no_urut' => 'No Urut',
            'grade' => 'Grade',
            'defect' => 'Defect',
            'lot_no' => 'Lot No',
            'ukuran' => 'Ukuran',
            'join_piece' => 'Join Piece',
            'keterangan' => 'Keterangan'
        ];
    }


}