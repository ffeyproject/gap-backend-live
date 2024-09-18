<?php
namespace backend\models\form;

use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use common\models\User;

class TrnMoPrintingForm extends TrnMo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'est_produksi','est_packing','target_shipment','approval_id','piece_length','jet_black','heat_cut','article','hanger','label','joint','joint_qty','selvedge_stamping','selvedge_continues','side_band','tag','folder','arsip','album','packing_method','shipping_method','shipping_sorting','plastic','face_stamping', 'note', 'design', 'border_size', 'block_size', 'foil', 'strike_off','persen_grading'], 'required'],
            [['date', 'est_produksi', 'est_packing', 'target_shipment'], 'date', 'format'=>'php:Y-m-d'],
            [['heat_cut', 'foil', 'joint', 'jet_black'], 'boolean'],
            [['sc_id','sc_greige_id','process','approval_id','joint_qty', 'packing_method', 'shipping_method', 'shipping_sorting', 'plastic', 'created_at', 'created_by', 'updated_at', 'updated_by', 'jenis_gudang'], 'integer'],
            [['strike_off', 'face_stamping', 'note'], 'string'],

            ['jenis_gudang', 'default', 'value'=>TrnStockGreige::JG_PFP],

            [['handling', 'design', 'article', 'selvedge_stamping', 'selvedge_continues', 'side_band', 'tag', 'hanger', 'label', 'folder', 'album', 'arsip', 'piece_length'], 'string', 'max' => 255],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::class, 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['approval_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approval_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::class, 'targetAttribute' => ['sc_id' => 'id']],

            ['packing_method', 'in', 'range' => [self::PACKING_METHOD_SINGLE_ROLL, self::PACKING_METHOD_DOUBLE_FOLDED]],
            ['shipping_method', 'in', 'range' => [self::SHIPPING_METHOD_BALE, self::SHIPPING_METHOD_CARTOON, self::SHIPPING_METHOD_LOSE]],
            ['shipping_sorting', 'in', 'range' => [self::SHIPPING_SORTING_SOLID, self::SHIPPING_SORTING_ASSORTED]],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CLOSED, self::STATUS_BATAL]],
            ['status', 'default', 'value'=>1]
        ];
    }
}