<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_sc_komisi".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_agen_id
 * @property int $sc_greige_id
 * @property int $tipe_komisi 1=PERSENTASE, 2=NOMINAL
 * @property float $komisi_amount
 *
 * @property TrnSc $sc
 * @property TrnScAgen $scAgen
 * @property TrnScGreige $scGreige
 *
 * @property string $namaAgen
 * @property string $greigeNamaKain
 * @property float $komisiTotal
 */
class
TrnScKomisi extends \yii\db\ActiveRecord
{
    const TIPE_KOMISI_PERSENTASE = 1;const TIPE_KOMISI_NOMINAL = 2;
    /**
     * @return array
    */
    public static function tipeKomisiOptions(){
        return [
            self::TIPE_KOMISI_PERSENTASE => 'PERSENTASE',
            self::TIPE_KOMISI_NOMINAL => 'NOMINAL'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_sc_komisi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_agen_id', 'sc_greige_id', 'tipe_komisi', 'komisi_amount'], 'required'],
            [['sc_id', 'sc_agen_id', 'sc_greige_id', 'tipe_komisi'], 'default', 'value' => null],
            [['sc_id', 'sc_agen_id', 'sc_greige_id', 'tipe_komisi'], 'integer'],
            [['komisi_amount'], 'number'],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_agen_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScAgen::className(), 'targetAttribute' => ['sc_agen_id' => 'id']],
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
            'sc_agen_id' => 'Sc Agen ID',
            'sc_greige_id' => 'Sc Greige ID',
            'tipe_komisi' => 'Tipe Komisi',
            'komisi_amount' => 'Komisi Amount',
        ];
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
    public function getScAgen()
    {
        return $this->hasOne(TrnScAgen::className(), ['id' => 'sc_agen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * @return string
     */
    public function getNamaAgen()
    {
        return $this->scAgen->nama_agen;
    }

    /**
     * @return string
     */
    public function getGreigeNamaKain()
    {
        $greiGroup = !empty($this->scGreige) ? $this->scGreige->greigeGroup : null;
        return !empty($greiGroup) ? $greiGroup->nama_kain : null;
    }

    /**
     * @return float
     */
    public function getKomisiTotal()
    {
        if($this->tipe_komisi == self::TIPE_KOMISI_PERSENTASE){
            return $this->scGreige->totalPrice * (floatval($this->komisi_amount) / 100);
        }else{
            $scGreige = $this->scGreige;
            //Komisi ($this->komisi_amount) dikali panjang pesanan berdasarkan finish unit yang ditentukan

            if($this->scGreige->price_param == TrnScGreige::PRICE_PARAM_PER_YARD){
                return floatval($this->komisi_amount) * $scGreige->qtyFinishToYard;
            }

            return floatval($this->komisi_amount) * $scGreige->qtyFinish;
        }
    }
}
