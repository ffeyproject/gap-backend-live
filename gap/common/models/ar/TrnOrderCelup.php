<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_order_celup".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $handling_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property float $qty
 * @property string|null $note
 * @property int $status 1=Draft, 2=Posted, 3=Processed
 * @property string $date
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $color
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnSc $sc
 * @property User $createdBy
 * @property User $updatedBy
 * @property MstHandling $handling
 * @property TrnKartuProsesCelup[] $trnKartuProsesCelups
 */
class TrnOrderCelup extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2; const STATUS_PROCESSED = 3;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_PROCESSED => 'Processed',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_order_celup';
    }

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
            [['sc_id', 'greige_id', 'qty', 'date', 'handling_id', 'color'], 'required'],
            [['sc_id', 'greige_group_id', 'greige_id', 'handling_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['sc_id', 'greige_group_id', 'greige_id', 'handling_id', 'no_urut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['qty'], 'number'],
            [['note'], 'string'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_PROCESSED]],

            [['no', 'color'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
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
            'greige_id' => 'Greige ID',
            'handling_id' => 'Handling ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'qty' => 'Qty',
            'note' => 'Note',
            'status' => 'Status',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'color' => 'Color'
        ];
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[GreigeGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
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
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Handling0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandling()
    {
        return $this->hasOne(MstHandling::className(), ['id' => 'handling_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelups()
    {
        return $this->hasMany(TrnKartuProsesCelup::className(), ['order_celup_id' => 'id']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnOrderPfp*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->asArray()
            ->one();

        if($lastData !== null){
            $this->no_urut = (int)$lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
