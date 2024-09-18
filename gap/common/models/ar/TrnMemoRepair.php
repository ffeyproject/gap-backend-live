<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_memo_repair".
 *
 * @property int $id
 * @property int $retur_buyer_id
 * @property int|null $sc_id
 * @property int|null $sc_greige_id
 * @property int|null $mo_id
 * @property int|null $wo_id
 * @property string $date
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $note
 * @property int $status 1=Draft, 2=Sedang Repair, 3=Selesai Repair, 4=Mutasi Ke Gudang Jadi
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $mutasi_at dimutasi ke gudang jadi pada
 * @property int $mutasi_by
 * @property string|null $mutasi_note catatan mutasi ke gudang jadi
 *
 * @property TrnReturBuyer $returBuyer
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 */
class TrnMemoRepair extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_ON_REPAIR = 2; const STATUS_REPAIRED = 3; const STATUS_INSPECTED = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_ON_REPAIR => 'Sedang Repair', self::STATUS_REPAIRED => 'Selesai Repair', self::STATUS_INSPECTED => 'Sudah Diinspect'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_memo_repair';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['retur_buyer_id', 'date', 'note'], 'required'],
            [['retur_buyer_id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'mutasi_at', 'mutasi_by'], 'default', 'value' => null],
            [['retur_buyer_id', 'sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'mutasi_at', 'mutasi_by'], 'integer'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_ON_REPAIR, self::STATUS_REPAIRED, self::STATUS_INSPECTED]],

            [['note', 'mutasi_note'], 'string'],
            [['no'], 'string', 'max' => 255],
            [['retur_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnReturBuyer::className(), 'targetAttribute' => ['retur_buyer_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'retur_buyer_id' => 'Retur Buyer ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'wo_id' => 'Wo ID',
            'date' => 'Date',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'note' => 'Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'mutasi_at' => 'Mutasi At',
            'mutasi_by' => 'Mutasi By',
            'mutasi_note' => 'Mutasi Note',
        ];
    }

    /**
     * Gets query for [[ReturBuyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturBuyer()
    {
        return $this->hasOne(TrnReturBuyer::className(), ['id' => 'retur_buyer_id']);
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

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/{$noUrut}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKirimMakloon*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one();

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
