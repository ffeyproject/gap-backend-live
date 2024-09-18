<?php
namespace backend\models\form;

use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnInspecting;
use yii\base\Model;

/**
 * This is the model class for table "trn_inspecting".
 *
 * @property int $jenis_gudang 1=Lokal, 2=Export, 3=Grade B
 */
class PenerimaanPackingForm extends Model
{
    public $jenis_gudang;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['jenis_gudang', 'required'],
            ['jenis_gudang', 'default', 'value'=>TrnGudangJadi::JENIS_GUDANG_LOKAL],
            ['jenis_gudang', 'in', 'range' => [TrnGudangJadi::JENIS_GUDANG_LOKAL, TrnGudangJadi::JENIS_GUDANG_EXPORT, TrnGudangJadi::JENIS_GUDANG_GRADE_B]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis_gudang'=>'Jenis Gudang'
        ];
    }
}