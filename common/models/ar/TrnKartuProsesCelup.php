<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "trn_kartu_proses_celup".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $order_celup_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $no_proses
 * @property int $asal_greige mereferensi ke model TrnStockGreige::asalGreigeOptions()
 * @property string|null $dikerjakan_oleh
 * @property string|null $lusi
 * @property string|null $pakan
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property int $status 1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected (Masuk gudang PFP), 6=Gagal Proses
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $delivered_at
 * @property int|null $delivered_by
 * @property string|null $reject_notes
 * @property string|null $berat
 * @property string|null $lebar
 * @property string|null $k_density_lusi density_lusi konstruksi greige
 * @property string|null $k_density_pakan density_pakan konstruksi greige
 * @property string|null $gramasi
 * @property string|null $lebar_preset
 * @property string|null $lebar_finish
 * @property string|null $berat_finish
 * @property string|null $t_density_lusi density_lusi target hasil jadi
 * @property string|null $t_density_pakan density_pakan target hasil jadi
 * @property string|null $handling
 * @property boolean $no_limit_item Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.
 *
 * @property KartuProcessCelupProcess[] $kartuProcessCelupProcesses
 * @property MstProcessDyeing[] $processes
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnOrderCelup $orderCelup
 * @property User $approvedBy
 * @property User $deliveredBy
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItems
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItemsTubeKiri
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItemsTubeKanan
 */
class TrnKartuProsesCelup extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_DELIVERED = 3;const STATUS_APPROVED = 4;const STATUS_INSPECTED = 5;const STATUS_GAGAL_PROSES = 6;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_DELIVERED => 'Delivered', self::STATUS_APPROVED => 'Disetujui', self::STATUS_INSPECTED => 'Diinspect', self::STATUS_GAGAL_PROSES=> 'Gagal Proses'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_celup';
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
            [['order_celup_id', 'asal_greige', 'date', 'lusi', 'pakan'], 'required'],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['note', 'reject_notes'], 'string'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_DELIVERED, self::STATUS_APPROVED, self::STATUS_INSPECTED, self::STATUS_GAGAL_PROSES]],

            ['no_limit_item', 'default', 'value'=>false],

            ['berat', 'number'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'lebar', 'k_density_lusi', 'k_density_pakan', 'gramasi', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['order_celup_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnOrderCelup::className(), 'targetAttribute' => ['order_celup_id' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['delivered_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['delivered_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_group_id' => 'Greige Group ID',
            'greige_id' => 'Greige ID',
            'order_celup_id' => 'Order Celup ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'no_proses' => 'No Proses',
            'asal_greige' => 'Asal Greige',
            'dikerjakan_oleh' => 'Dikerjakan Oleh',
            'lusi' => 'Lusi',
            'pakan' => 'Pakan',
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
            'delivered_at' => 'Delivered At',
            'delivered_by' => 'Delivered By',
            'reject_notes' => 'Reject Notes',
            'berat' => 'Berat',
            'lebar' => 'Lebar',
            'k_density_lusi' => 'Density Lusi Konstruksi Greige',
            'k_density_pakan' => 'Density Pakan Konstruksi Greige',
            'gramasi' => 'Gramasi',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            't_density_lusi' => 'Density Lusi Target Hasil Jadi',
            't_density_pakan' => 'Density Pakan Target Hasil Jadi',
            'handling' => 'Handling',
            'no_limit_item' => 'Item Tidak Dibatasi'
        ];
    }

    /**
     * Gets query for [[KartuProcessCelupProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessCelupProcesses()
    {
        return $this->hasMany(KartuProcessCelupProcess::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcesses()
    {
        return $this->hasMany(MstProcessDyeing::className(), ['id' => 'process_id'])->viaTable('kartu_process_celup_process', ['kartu_process_id' => 'id']);
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
     * Gets query for [[OrderCelup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderCelup()
    {
        return $this->hasOne(TrnOrderCelup::className(), ['id' => 'order_celup_id']);
    }

    /**
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * Gets query for [[DeliveredBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveredBy()
    {
        return $this->hasOne(User::className(), ['id' => 'delivered_by']);
    }

    /**
     * Gets query for [[TrnKartuProsesCelupItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelupItems()
    {
        return $this->hasMany(TrnKartuProsesCelupItem::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelupItemsTubeKiri()
    {
        return $this->getTrnKartuProsesCelupItems()->where(['tube'=>TrnKartuProsesCelupItem::TUBE_KIRI]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesCelupItemsTubeKanan()
    {
        return $this->getTrnKartuProsesCelupItems()->where(['tube'=>TrnKartuProsesCelupItem::TUBE_KANAN]);
    }

    /**
     * @param string|int $processId
     * @param array $modelsMstProcessDyeing MstProcessDyeing::toArray
     * @return bool
     */
    public function isProcessDone($processId, $modelsMstProcessDyeing){
        unset($modelsMstProcessDyeing['id']);unset($modelsMstProcessDyeing['order']);unset($modelsMstProcessDyeing['max_pengulangan']);unset($modelsMstProcessDyeing['nama_proses']);unset($modelsMstProcessDyeing['created_at']);unset($modelsMstProcessDyeing['created_by']);unset($modelsMstProcessDyeing['updated_at']);unset($modelsMstProcessDyeing['updated_by']);

        /* @var $modelProcessItem KartuProcessPfpProcess*/
        $modelProcessItem = $this->getKartuProcessCelupProcesses()->andWhere(['process_id'=>$processId])->one();

        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        //BaseVarDumper::dump($processItems, 10, true);Yii::$app->end();

        foreach ($modelsMstProcessDyeing as $key=>$value) {
            if($value){
                if(!isset($processItems[$key]) || empty($processItems[$key])){
                    return false;
                }
            }
        }

        /*BaseVarDumper::dump([
            '$processName'=>$processName
            '$processItems'=>$processItems,
            '$modelsMstProcessPfp'=>$modelsMstProcessPfp
        ], 10, true);Yii::$app->end();*/

        return true;
    }

    /**
     * @return boolean
     */
    public function getIsAllProcessDone(){
        foreach (MstProcessDyeing::find()->orderBy('order ASC')->asArray()->all() as $item) {
            if(!$this->isProcessDone($item['id'], $item)){
                return false;
            }
        }

        return true;
    }

    /**
     * @param string|int $processId
     * @return boolean
     */
    public function isReProcess($processId){
        /* @var $modelProcessItem KartuProcessCelupProcess*/
        $modelProcessItem = $this->getKartuProcessCelupProcesses()->andWhere(['process_id'=>$processId])->one();
        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        if(!isset($processItems['pengulangan'])){
            return false;
        }

        return true;
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

    public function setNomorProses(){
        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no_proses = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesPfp*/
        $lastData = self::find()
            ->select('id, no_urut, date')
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
                ['greige_id'=>$this->greige_id]
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
