<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;

/**
 * This is the model class for table "trn_kartu_proses_pfp".
 *
 * @property int $id
 * @property int $greige_group_id
 * @property int $greige_id
 * @property int $order_pfp_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $no_proses
 * @property int $asal_greige mereferensi ke model TrnStockGreige::asalGreigeOptions()
 * @property string|null $dikerjakan_oleh
 * @property string|null $lusi
 * @property string|null $pakan
 * @property string|null $note
 * @property string $date
 *
 * @property string|null $berat
 * @property string|null $lebar
 * @property string|null $k_density_lusi density_lusi konstruksi greige
 * @property string|null $k_density_pakan density_pakan konstruksi greige
 * @property string|null $gramasi
 *
 * @property string|null $lebar_preset
 * @property string|null $lebar_finish
 * @property string|null $berat_finish
 * @property string|null $t_density_lusi density_lusi target hasil jadi
 * @property string|null $t_density_pakan density_pakan target hasil jadi
 * @property string|null $handling
 *
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
 * @property boolean $no_limit_item Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.
 * @property string $nomor_kartu
 *
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnOrderPfp $orderPfp
 * @property User $approvedBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deliveredBy
 * @property KartuProcessPfpProcess[] $kartuProcessPfpProcesses
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItems
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItemsTubeKiri
 * @property TrnKartuProsesPfpItem[] $trnKartuProsesPfpItemsTubeKanan
 *
 * @property boolean $isAllProcessDone
 */
class TrnKartuProsesPfp extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_DELIVERED = 3;const STATUS_APPROVED = 4;const STATUS_INSPECTED = 5;const STATUS_GAGAL_PROSES = 6;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_DELIVERED => 'Delivered', self::STATUS_APPROVED => 'Disetujui', self::STATUS_INSPECTED => 'Masuk Stock Gudang', self::STATUS_GAGAL_PROSES=> 'Gagal Proses'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_pfp';
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
            [['order_pfp_id', 'asal_greige', 'date', 'lusi', 'pakan', 'nomor_kartu'], 'required'],
            [['greige_group_id', 'greige_id', 'order_pfp_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'order_pfp_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['note', 'reject_notes', 'nomor_kartu'], 'string'],

            [['date'], 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_DELIVERED, self::STATUS_APPROVED, self::STATUS_INSPECTED, self::STATUS_GAGAL_PROSES]],
            ['no_limit_item', 'default', 'value'=>false],

            ['berat', 'number'],

            [['nomor_kartu', 'greige_id'], 'unique', 'targetAttribute' => ['nomor_kartu', 'greige_id']],

            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'lebar', 'k_density_lusi', 'k_density_pakan', 'gramasi', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['greige_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreigeGroup::className(), 'targetAttribute' => ['greige_group_id' => 'id']],
            [['order_pfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnOrderPfp::className(), 'targetAttribute' => ['order_pfp_id' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'order_pfp_id' => 'Order Pfp ID',
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
            'no_limit_item' => 'Item Tidak Dibatasi',
            'nomor_kartu' => 'Nomor Kartu',
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
    public function getGreigeGroup()
    {
        return $this->hasOne(MstGreigeGroup::className(), ['id' => 'greige_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderPfp()
    {
        return $this->hasOne(TrnOrderPfp::className(), ['id' => 'order_pfp_id']);
    }

    /**
     * Gets query for [[KartuProcessPfpProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessPfpProcesses()
    {
        return $this->hasMany(KartuProcessPfpProcess::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProcesses()
    {
        return $this->hasMany(MstProcessPfp::className(), ['id' => 'process_id'])->viaTable('kartu_process_pfp_process', ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveredBy()
    {
        return $this->hasOne(User::className(), ['id' => 'delivered_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItems()
    {
        return $this->hasMany(TrnKartuProsesPfpItem::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItemsTubeKiri()
    {
        return $this->getTrnKartuProsesPfpItems()->where(['tube'=>TrnKartuProsesPfpItem::TUBE_KIRI]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPfpItemsTubeKanan()
    {
        return $this->getTrnKartuProsesPfpItems()->where(['tube'=>TrnKartuProsesPfpItem::TUBE_KANAN]);
    }

    /**
     * @param string|int $processId
     * @param array $modelsMstProcessPfp MstProcessPfp::toArray
     * @return bool
     */
    public function isProcessDone($processId, $modelsMstProcessPfp){
        //$processName = $modelsMstProcessPfp['nama_proses'];
        unset($modelsMstProcessPfp['id']);unset($modelsMstProcessPfp['order']);unset($modelsMstProcessPfp['max_pengulangan']);unset($modelsMstProcessPfp['nama_proses']);unset($modelsMstProcessPfp['created_at']);unset($modelsMstProcessPfp['created_by']);unset($modelsMstProcessPfp['updated_at']);unset($modelsMstProcessPfp['updated_by']);

        /* @var $modelProcessItem KartuProcessPfpProcess*/
        $modelProcessItem = $this->getKartuProcessPfpProcesses()->andWhere(['process_id'=>$processId])->one();

        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        //BaseVarDumper::dump($processItems, 10, true);Yii::$app->end();

        foreach ($modelsMstProcessPfp as $key=>$value) {
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
        foreach (MstProcessPfp::find()->orderBy('order ASC')->asArray()->all() as $item) {
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
        /* @var $modelProcessItem KartuProcessPfpProcess*/
        $modelProcessItem = $this->getKartuProcessPfpProcesses()->andWhere(['process_id'=>$processId])->one();
        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        if(!isset($processItems['pengulangan'])){
            return false;
        }

        return true;
    }

    public function getTanggalKartuProcessPfpProcess(){
        /* @var $modelProcessItem KartuProcessDyeingProcess*/
        $model = $this->getKartuProcessPfpProcesses()->andWhere(['process_id'=>1])->one();
        if($model !== null){
            try {
                $model = \yii\helpers\Json::decode($model['value']);
                if(!isset($model['tanggal'])){
                    return null;
                }
                return $model['tanggal'];
            }catch (Throwable $t){
                return null;
            }
        }
        return null;
    }

    public function setNomor(){
        /*$this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";*/

        $this->setNoUrut();
        $namaGreige = $this->greige->nama_kain;
        $this->no = $namaGreige.'/'.$this->nomor_kartu;
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
                ['greige_id'=>$this->greige_id],
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
