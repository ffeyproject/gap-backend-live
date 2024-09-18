<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "trn_mo_color".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property string $color
 * @property float $qty
 *
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWoColor[] $trnWoColors
 *
 * @property TrnWoColor[] $trnWoColorsDraft
 * @property TrnWoColor[] $trnWoColorsTurun
 * @property TrnWoColor[] $trnWoColorsBatal
 *
 * @property float $qtyWoColors
 * @property float $qtyWoColorsDraft
 * @property float $qtyWoColorsTurun
 * @property float $qtyWoColorsBatal
 *
 * @property float $qtyBatchToMeter
 * @property float $qtyFinish
 * @property float $qtyFinishToYard
 */
class TrnMoColor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_mo_color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mo_id', 'color', 'qty'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id'], 'integer'],
            [['qty'], 'number'],
            [['color'], 'string', 'max' => 255],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
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
            'color' => 'Color',
            'qty' => 'Qty (Batch)',
        ];
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
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['mo_color_id' => 'id']);
    }

    /**
     * TrnWoColor yang WO nya sudah diposting tapi belum disetujui
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsDraft()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_DRAFT]);
    }

    /**
     * TrnWoColor yang WO nya sudah diposting tapi belum disetujui
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsPosted()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_POSTED]);
    }

    /**
     * TrnWoColor yang WO nya sudah disetujui
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsTurun()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_APPROVED]);
    }

    /**
     * TrnWoColor yang WO nya dibatalkan
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColorsBatal()
    {
        return $this->getTrnWoColors()->joinWith('wo')->where(['trn_wo.status'=>TrnWo::STATUS_BATAL]);
    }

    /**
     * @return float
     */
    public function getQtyBatchToUnit()
    {
        return floatval($this->qty) * $this->scGreige->greigeGroup->qty_per_batch;
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyBatchToMeter()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->getQtyBatchToUnit());
            case MstGreigeGroup::UNIT_METER:
                return $this->getQtyBatchToUnit();
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
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->getQtyBatchToUnit());
            case MstGreigeGroup::UNIT_YARD:
                return $this->getQtyBatchToUnit();
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getQtyFinish()
    {
        return floatval($this->qty) * $this->scGreige->greigeGroup->qtyFinish;
    }

    /**
     * @return float
     */
    public function getQtyFinishToYard()
    {
        switch ($this->scGreige->greigeGroup->unit){
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->qtyFinish);
            case MstGreigeGroup::UNIT_YARD:
                return $this->qtyFinish;
            default:
                return 0;
        }
    }

    /**
     * @return float
     */
    public function getQtyFinishToMeter()
    {
        switch ($this->scGreige->greigeGroup->unit){
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
    public function getQtyWoColors()
    {
        $qty = $this->getTrnWoColors()->sum('qty');
        return $qty === null ? 0 : $qty;
    }

    /**
     * @return float
     */
    public function getQtyWoColorsDraft()
    {
        $qty = $this->getTrnWoColorsDraft()->sum('qty');
        return $qty === null ? 0 : $qty;
    }

    /**
     * @return float
     */
    public function getQtyWoColorsTurun()
    {
        $qty = $this->getTrnWoColorsTurun()->sum('qty');
        return $qty === null ? 0 : $qty;
    }

    /**
     * @return float
     */
    public function getQtyWoColorsBatal()
    {
        $qty = $this->getTrnWoColorsBatal()->sum('qty');
        return $qty === null ? 0 : $qty;
    }
}
