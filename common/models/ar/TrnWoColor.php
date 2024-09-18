<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "trn_wo_color".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int $greige_id Mereferensi greige pada WO
 * @property int $mo_color_id mengacu terhadap color pada MO
 * @property float $qty
 * @property string $note
 *
 * @property TrnMo $mo
 * @property MstGreige $greige
 * @property TrnMoColor $moColor
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 *
 * @property float $qtyBatchToMeter
 * @property float $qtyBatchToUnit
 * @property float $qtyFinish
 * @property float $qtyFinishToMeter
 * @property float $qtyFinishToYard
 */
class TrnWoColor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_wo_color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'greige_id', 'mo_color_id', 'qty', 'note'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'greige_id', 'mo_color_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'greige_id', 'mo_color_id'], 'integer'],
            [['qty'], 'number'],
            [['note'], 'string'],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['mo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMoColor::className(), 'targetAttribute' => ['mo_color_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
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
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'mo_color_id' => 'Mo Color ID',
            'qty' => 'Qty',
            'note' => 'Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoColor()
    {
        return $this->hasOne(TrnMoColor::className(), ['id' => 'mo_color_id']);
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
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * @return float
     */
    public function getQtyBatchToUnit()
    {
        $gap = $this->greige->gap;
        return $this->qty * ($this->scGreige->greigeGroup->qty_per_batch + $gap);
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyBatchToMeter()
    {
        switch ($this->greige->group->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->qtyBatchToUnit);
            case MstGreigeGroup::UNIT_METER:
                return $this->qtyBatchToUnit;
            default:
                return 0;
        }
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyBatchToYard()
    {
        switch ($this->greige->group->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->qtyBatchToUnit);
            case MstGreigeGroup::UNIT_YARD:
                return $this->qtyBatchToUnit;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getQtyFinish()
    {
        $susut = $this->scGreige->greigeGroup->nilai_penyusutan;
        return $this->qtyBatchToUnit * (1 - ($susut/100));
    }

    /**
     * @return float
     */
    public function getQtyFinishToMeter()
    {
        switch ($this->greige->group->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->qtyFinish);
            case MstGreigeGroup::UNIT_METER:
                return $this->qtyFinish;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getQtyFinishToYard()
    {
        switch ($this->greige->group->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->qtyFinish);
            case MstGreigeGroup::UNIT_YARD:
                return $this->qtyFinish;
            default:
                return 0;
        }
    }
}
