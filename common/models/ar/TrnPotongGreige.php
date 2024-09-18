<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_potong_greige".
 *
 * @property int $id
 * @property int $stock_greige_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by yang memerintahkan pemotongan jika ada
 * @property int $status 1=draft, 2=posted, 3=approved 4=rejected
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property TrnStockGreige $stockGreige
 * @property TrnPotongGreigeItem[] $trnPotongGreigeItems
 */
class TrnPotongGreige extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;const STATUS_REJECTED = 4;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved', self::STATUS_REJECTED => 'Rejected'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_potong_greige';
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
            [['stock_greige_id', 'date'], 'required'],
            [['stock_greige_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['stock_greige_id', 'no_urut', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            [['no'], 'string', 'max' => 255],
            [
                'stock_greige_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => TrnStockGreige::className(),
                'targetAttribute' => ['stock_greige_id' => 'id'],
                'filter' => ['status'=>TrnStockGreige::STATUS_VALID]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_greige_id' => 'Stock Greige ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'note' => 'Note',
            'date' => 'Date',
            'posted_at' => 'Posted At',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'stock_greige_id' => 'ID dari stock greige, silahkan cari referensi nya di menu gudang greige, lalu paste id nya disini.',
            'approved_by' => 'Yang memerintahkan pemotongan greige. Boleh dikosongkan.',
            'note' => 'Keterangan, bisa menuliskan id atau nomor kartu proses referensi, siapa yang memerintahkan, dan sebagainya.',
        ];
    }

    /**
     * Gets query for [[StockGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockGreige()
    {
        return $this->hasOne(TrnStockGreige::className(), ['id' => 'stock_greige_id']);
    }

    /**
     * Gets query for [[TrnPotongGreigeItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnPotongGreigeItems()
    {
        return $this->hasMany(TrnPotongGreigeItem::className(), ['potong_greige_id' => 'id']);
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$yearTwoDigit}/{$month}/$noUrut";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesMaklon*/
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
