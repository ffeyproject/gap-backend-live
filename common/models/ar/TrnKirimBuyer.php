<?php

namespace common\models\ar;

use backend\components\Converter;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "trn_kirim_buyer".
 *
 * @property int $id
 * @property int $header_id
 * @property int|null $sc_id
 * @property int|null $sc_greige_id
 * @property int|null $mo_id
 * @property int $wo_id
 * @property string|null $nama_kain_alias nama kain untuk buyer
 * @property int $unit Mengacu pada MstGreigeGroup::unitOptions()
 * @property string|null $note
 *
 * @property TrnKirimBuyerHeader $header
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property TrnKirimBuyerItem[] $trnKirimBuyerItems
 *
 * @property int $qtyKirim
 * @property int $qtyKirimToYard
 * @property int $qtyKirimToMeter
 */
class TrnKirimBuyer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kirim_buyer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wo_id', 'header_id'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'header_id'], 'integer'],

            ['unit', 'default', 'value'=>MstGreigeGroup::UNIT_METER],
            ['unit', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            [['note'], 'string'],
            [['no', 'nama_kain_alias'], 'string', 'max' => 255],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['header_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnKirimBuyerHeader::className(), 'targetAttribute' => ['header_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header_id' => 'Header ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'nama_kain_alias' => 'Nama Kain Alias',
            'unit' => 'Unit',
            'note' => 'Note',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){
                $this->mo_id = $this->wo->mo_id;
                $this->sc_greige_id = $this->mo->sc_greige_id;
                $this->sc_id = $this->scGreige->sc_id;
            }

            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * Gets query for [[Wo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHeader()
    {
        return $this->hasOne(TrnKirimBuyerHeader::className(), ['id' => 'header_id']);
    }

    /**
     * Gets query for [[TrnKirimBuyerItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKirimBuyerItems()
    {
        return $this->hasMany(TrnKirimBuyerItem::className(), ['kirim_buyer_id' => 'id']);
    }

    /**
     *
     * @return int
     */
    public function getQtyKirim()
    {
        return $this->getTrnKirimBuyerItems()->sum('qty');
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyKirimToMeter(){
        switch ($this->unit){
            case MstGreigeGroup::UNIT_YARD:
                return Converter::yardToMeter($this->qtyKirim);
            case MstGreigeGroup::UNIT_METER:
                return $this->qtyKirim;
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->scGreige->greigeGroup->unit].' belum didukung');
        }
    }

    /**
     * @return float
     * @throws NotAcceptableHttpException
     */
    public function getQtyKirimToYard(){
        switch ($this->unit){
            case MstGreigeGroup::UNIT_YARD:
                return $this->qtyKirim;
            case MstGreigeGroup::UNIT_METER:
                return Converter::meterToYard($this->qtyKirim);
            case MstGreigeGroup::UNIT_KILOGRAM:
                return 0;
            default:
                throw new NotAcceptableHttpException('Unit '.MstGreigeGroup::unitOptions()[$this->scGreige->greigeGroup->unit].' belum didukung');
        }
    }
}
