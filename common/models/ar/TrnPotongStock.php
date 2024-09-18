<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "trn_potong_stock".
 *
 * @property int $id
 * @property int $stock_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $note
 * @property string $date
 * @property string|null $diperintahkan_oleh
 * @property int $status 1=draft, 2=posted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property TrnGudangJadi $stock
 * @property TrnPotongStockItem[] $trnPotongStockItems
 *
 * @property string greigeGroupNamaKain
 */
class TrnPotongStock extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_potong_stock';
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
            [['stock_id', 'date'], 'required'],
            [['stock_id', 'no_urut', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['stock_id', 'no_urut', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range'=>[self::STATUS_DRAFT, self::STATUS_POSTED]],
            [['note'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['no', 'diperintahkan_oleh'], 'string', 'max' => 255],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnGudangJadi::className(), 'targetAttribute' => ['stock_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_id' => 'Stock ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'note' => 'Note',
            'date' => 'Date',
            'diperintahkan_oleh' => 'Diperintahkan Oleh',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'stock_id' => 'ID dari stock gudang jadi, silahkan cari referensi nya di menu gudang jadi, lalu paste id nya disini.',
            'diperintahkan_oleh' => 'Yang memerintahkan pemotongan stock. Boleh dikosongkan.',
            'note' => 'Keterangan, bisa menuliskan id atau nomor kartu proses referensi, siapa yang memerintahkan, dan sebagainya.',
        ];
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(TrnGudangJadi::className(), ['id' => 'stock_id']);
    }

    /**
     * Gets query for [[TrnPotongStockItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnPotongStockItems()
    {
        return $this->hasMany(TrnPotongStockItem::className(), ['potong_stock_id' => 'id']);
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

        /* @var $lastData array*/
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
