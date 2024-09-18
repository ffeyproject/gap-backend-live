<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_sc_greige".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $greige_group_id
 * @property int $process 1=DYEING, 2=PRINTING, 3=GREIGE, 4=PFP
 * @property int $lebar_kain 1=44, 2=58
 * @property string $merek
 * @property int $grade 1=A, 2=B, 3=C, 4=ALL GRADE
 * @property string $piece_length
 * @property float $unit_price
 * @property string $price_param 1=Per Unit, 2=Per Yard
 * @property float $qty
 * @property string|null $woven_selvedge
 * @property string|null $note
 * @property bool|null $closed
 * @property string|null $closing_note
 * @property string|null $no_order_greige
 * @property int|null $no_urut_order_greige
 * @property string|null $order_greige_note
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnMo[] $trnMos
 * @property TrnMoColor[] $trnMoColors
 * @property MstGreigeGroup $greigeGroup
 * @property TrnSc $sc
 * @property TrnScKomisi[] $trnScKomisis
 * @property TrnWo[] $trnWos
 * @property TrnWoColor[] $trnWoColors
 */
class TrnScGreige extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_greige';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'greige_group_id', 'process', 'lebar_kain', 'merek', 'grade', 'piece_length', 'unit_price', 'price_param', 'qty'], 'required'],
            [['sc_id', 'greige_group_id', 'process', 'lebar_kain', 'grade', 'no_urut_order_greige'], 'default', 'value' => null],
            [['sc_id', 'greige_group_id', 'process', 'lebar_kain', 'grade', 'no_urut_order_greige'], 'integer'],
            [['unit_price', 'qty'], 'number'],
            [['woven_selvedge', 'note', 'closing_note', 'order_greige_note'], 'string'],
            [['closed'], 'boolean'],
            [['merek', 'no_order_greige'], 'string', 'max' => 255],
            [['piece_length', 'price_param'], 'string', 'max' => 100],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'greige_group_id' => 'Greige Group ID',
            'process' => 'Process',
            'lebar_kain' => 'Lebar Kain',
            'merek' => 'Merek',
            'grade' => 'Grade',
            'piece_length' => 'Piece Length',
            'unit_price' => 'Unit Price',
            'price_param' => 'Price Param',
            'qty' => 'Qty',
            'woven_selvedge' => 'Woven Selvedge',
            'note' => 'Note',
            'closed' => 'Closed',
            'closing_note' => 'Closing Note',
            'no_order_greige' => 'No Order Greige',
            'no_urut_order_greige' => 'No Urut Order Greige',
            'order_greige_note' => 'Order Greige Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMoColors()
    {
        return $this->hasMany(TrnMoColor::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScKomisis()
    {
        return $this->hasMany(TrnScKomisi::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['sc_greige_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['sc_greige_id' => 'id']);
    }
}
