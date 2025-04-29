<?php
namespace backend\models\form;

use common\models\ar\MstGreigeGroup;
use common\models\ar\MstK3l;
use yii\base\Model;

/**
 *
 */
class InspectingHeaderForm extends Model
{
    public $tgl_kirim;
    public $kartu_proses_id;
    public $tgl_inspeksi;
    public $k3l_code;
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
            [['tgl_kirim', 'kartu_proses_id', 'tgl_inspeksi', 'no_lot', 'status', 'jenis_order', 'k3l_code', 'jenis_inspek'], 'required'],
            ['status', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],
            [['k3l_code'], 'exist', 'skipOnError' => true, 'targetClass' => MstK3l::className(), 'targetAttribute' => ['k3l_code' => 'k3l_code']],
            [['tgl_kirim', 'tgl_inspeksi'], 'date', 'format'=>'php:Y-m-d'],
            [['no_lot'], 'string', 'max' => 255],
            ['jenis_order', 'in', 'range' => ['dyeing', 'printing', 'memo_repair']],
        ];
    }
}