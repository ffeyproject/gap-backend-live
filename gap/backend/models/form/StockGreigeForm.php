<?php
namespace backend\models\form;

use common\models\ar\MstGreige;
use yii\base\Model;

/**
 * This is the model class for table "trn_stock_greige".
 *
 * @property int $greige_id
 * @property int $asal_greige
 * @property string $no_lapak
 * @property string $lot_lusi
 * @property string $lot_pakan
 * @property int $status_tsd 1=sm(salur muda),2=st(salur tua),3=sa(salur abnormal
 * @property string $no_document
 * @property string $pengirim
 * @property string $mengetahui
 * @property string|null $note
 */
class StockGreigeForm extends Model
{
    public $greige_id;
    public $asal_greige;
    public $no_lapak;
    public $lot_lusi;
    public $lot_pakan;
    public $status_tsd;
    public $no_document;
    public $pengirim;
    public $mengetahui;
    public $note;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_id', 'asal_greige', 'lot_lusi', 'lot_pakan', 'status_tsd', 'no_document', 'pengirim', 'mengetahui'], 'required'],
            [['greige_id', 'status_tsd'], 'default', 'value' => null],
            [['greige_id', 'status_tsd'], 'integer'],
            [['note'], 'string'],
            ['no_lapak', 'default', 'value'=>'-'],
            [['no_lapak', 'lot_lusi', 'lot_pakan', 'no_document', 'pengirim', 'mengetahui'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'greige_id' => 'Greige ID',
            'asal_greige' => 'Asal Greige',
            'no_lapak' => 'No Lapak',
            'lot_lusi' => 'Lot Lusi',
            'lot_pakan' => 'Lot Pakan',
            'status_tsd' => 'Ket. Weaving',
            'no_document' => 'No Document',
            'pengirim' => 'Pengirim',
            'mengetahui' => 'Mengetahui',
            'note' => 'Note',
        ];
    }
}