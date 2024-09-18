<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\BaseVarDumper;

/**
 * This is the model class for table "trn_memo_perubahan_data".
 *
 * @property int $id
 * @property string $description
 * @property string $date
 * @property int $status 1=Draft 2=Posted
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $no_urut
 * @property string|null $no
 *
 * @property User $createdBy
 * @property string $statusName
 * @property string $creatorName
 */
class TrnMemoPerubahanData extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1; const STATUS_POSTED = 2;
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
        return 'trn_memo_perubahan_data';
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
            [['description', 'date'], 'required'],
            [['description'], 'string'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED]],

            [['created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'default', 'value' => null],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['no'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'date' => 'Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'statusName' => 'Status',
            'creatorName' => 'Dibuat Oleh'
        ];
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
     * @return string
     */
    public function getStatusName()
    {
        return self::statusOptions()[$this->status];
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->createdBy->full_name;
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
            ->one()
        ;

        /*BaseVarDumper::dump([
            '$ym'=>$ym,
            '$lastData'=>$lastData
        ], 10, true);Yii::$app->end();*/

        if(!is_null($lastData)){
            $this->no_urut = $lastData['no_urut'] + 1;
        }else{
            $this->no_urut = 1;
        }
    }
}
