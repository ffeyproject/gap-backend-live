<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trn_gudang_stock_opname_item".
 *
 * @property int $id
 * @property int $trn_gudang_stock_opname_id
 * @property float $panjang_m
 * @property string $no_set_lusi
 * @property string $grade
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TrnGudangStockOpname $trnGudangStockOpname
 */
class TrnGudangStockOpnameItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'trn_gudang_stock_opname_item';
    }

    const GRADE_A = 1;const GRADE_B = 2;const GRADE_C = 3;const GRADE_D = 4;const GRADE_E = 5;const GRADE_NG = 6;const GRADE_A_PLUS = 7;const GRADE_A_ASTERISK = 8; const GRADE_PUTIH = 9;
    /**
     * @return array
     */
    public static function gradeOptions(){
        return [
            self::GRADE_A => 'A', self::GRADE_B => 'B', self::GRADE_C => 'C', self::GRADE_D => 'D', self::GRADE_E => 'E', self::GRADE_NG => 'NG', self::GRADE_A_PLUS => 'A+', self::GRADE_A_ASTERISK => 'A*', self::GRADE_PUTIH => 'Putih',
        ];
    }

    public function rules()
{
    return [
        // hanya no_set_lusi dan grade yang wajib, opname_id boleh null
        [['no_set_lusi', 'grade'], 'required'],

        // integer tapi boleh null
        [['trn_gudang_stock_opname_id'], 'integer'],

        // field numeric/string lainnya
        [['ket_defect'], 'string', 'max' => 100],
        [['panjang_m'], 'number'],
        [['created_at', 'updated_at'], 'safe'],
        [['no_set_lusi'], 'string', 'max' => 50],
        [['grade'], 'string', 'max' => 10],

        // is_out boolean default false
        ['is_out', 'boolean'],
        ['is_out', 'default', 'value' => false],

        // jika diisi, harus exist di tabel parent
        [
            ['trn_gudang_stock_opname_id'],
            'exist',
            'skipOnError' => true,
            'targetClass' => TrnGudangStockOpname::class,
            'targetAttribute' => ['trn_gudang_stock_opname_id' => 'id']
        ],
    ];
}

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trn_gudang_stock_opname_id' => 'Gudang Stock Opname',
            'panjang_m' => 'Panjang (m)',
            'no_set_lusi' => 'No Set Lusi',
            'grade' => 'Grade',
            'ket_defect' => 'Ket Defect',
            'created_at' => 'Dibuat pada',
            'updated_at' => 'Diperbarui pada',
            'status' => 'Status Header',
        ];
    }

    public function getTrnGudangStockOpname()
    {
        return $this->hasOne(TrnGudangStockOpname::class, ['id' => 'trn_gudang_stock_opname_id']);
    }

    public function getGreige(){
        return $this->trnGudangStockOpname->greige;
    }


    

    
}