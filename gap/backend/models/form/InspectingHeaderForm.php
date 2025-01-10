<?php
namespace backend\models\form;

use common\models\ar\MstGreigeGroup;
use yii\base\Model;

/**
 *
 */
class InspectingHeaderForm extends Model
{
    public $tgl_kirim;
    public $kartu_proses_id;
    public $tgl_inspeksi;
    public $no_lot;
    public $status;
    public $jenis_order;
    public $jenis_inspek;

    /**
     * @inheritDoc
    */
    public function rules()
    {
        return [
            [['tgl_kirim', 'kartu_proses_id', 'tgl_inspeksi', 'no_lot', 'status', 'jenis_order'], 'required'],
            ['status', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],
            [['tgl_kirim', 'tgl_inspeksi'], 'date', 'format'=>'php:Y-m-d'],
            [['no_lot'], 'string', 'max' => 255],
            ['jenis_order', 'in', 'range' => ['dyeing', 'printing', 'memo_repair']],
            ['jenis_inspek', 'in', 'range' => ['Fresh Order', 'Re-Inspeksi']],
        ];
    }
}