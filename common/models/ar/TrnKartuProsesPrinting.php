<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "trn_kartu_proses_printing".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int|null $kartu_proses_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string|null $no_proses
 * @property int $asal_greige 1=Water Jet Loom, 2=Beli, 3=Rapier
 * @property string|null $dikerjakan_oleh
 * @property string|null $lusi
 * @property string|null $pakan
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property int $status 1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected, 6=Ganti Greige (proses gagal dan dibuat memo pengantian greige), 8=Batal
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $memo_pg memo penggantian greige
 * @property int|null $memo_pg_at
 * @property int|null $memo_pg_by
 * @property string|null $memo_pg_no
 * @property int|null $delivered_at
 * @property int|null $delivered_by
 * @property string|null $reject_notes
 * @property int $wo_color_id
 * @property string|null $kombinasi
 * @property string|null $berat
 * @property boolean $no_limit_item Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.
 * @property string $nomor_kartu
 *
 * @property KartuProcessPrintingProcess[] $kartuProcessPrintingProcesses
 * @property MstProcessPrinting[] $processes
 * @property TrnKartuProsesPrinting $kartuProses
 * @property TrnInspecting[] $trnInspectings
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property TrnWoColor $woColor
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 */
class TrnKartuProsesPrinting extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_DELIVERED = 3;const STATUS_APPROVED = 4;const STATUS_INSPECTED = 5;const STATUS_GANTI_GREIGE = 6;const STATUS_GANTI_GREIGE_LINKED = 7;const STATUS_BATAL = 8;
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_DELIVERED => 'Delivered', self::STATUS_APPROVED => 'Disetujui/Sedang Diinspect', self::STATUS_INSPECTED => 'Selesai Diinspect', self::STATUS_GANTI_GREIGE=> 'Ganti Greige', self::STATUS_GANTI_GREIGE_LINKED=> 'Ganti Greige Linked', self::STATUS_BATAL=> 'Batal'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_printing';
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
            [['wo_color_id', 'asal_greige', 'date', 'dikerjakan_oleh', 'lusi', 'pakan', 'nomor_kartu'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'delivered_at', 'delivered_by'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'delivered_at', 'delivered_by', 'wo_color_id'], 'integer'],
            [['note', 'memo_pg', 'reject_notes', 'nomor_kartu'], 'string'],
            ['date', 'date', 'format'=>'php:Y-m-d'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['no_limit_item', 'default', 'value'=>false],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'memo_pg_no', 'kombinasi', 'berat'], 'string', 'max' => 255],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['wo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWoColor::className(), 'targetAttribute' => ['wo_color_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            ['nomor_kartu', 'unique'],
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
            'wo_id' => 'Wo ID',
            'kartu_proses_id' => 'Kartu Proses ID',
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
            'memo_pg' => 'Memo Pg',
            'memo_pg_at' => 'Memo Pg At',
            'memo_pg_by' => 'Memo Pg By',
            'memo_pg_no' => 'Memo Pg No',
            'delivered_at' => 'Delivered At',
            'delivered_by' => 'Delivered By',
            'reject_notes' => 'Reject Notes',
            'wo_color_id' => 'WO Color Id',
            'kombinasi' => 'Kombinasi',
            'no_limit_item' => 'Item Tidak Dibatasi',
            'nomor_kartu' => 'Nomor Kartu',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'kartu_proses_id' => 'Nomor kartu proses gagal yang akan diproses ulang. Jika diisi, maka nomor WO tidak perlu diisi.',
        ];
    }

    /**
     * Gets query for [[KartuProcessDyeingProcesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProcessPrintingProcesses()
    {
        return $this->hasMany(KartuProcessPrintingProcess::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProcesses()
    {
        return $this->hasMany(MstProcessPrinting::className(), ['id' => 'process_id'])->viaTable('kartu_process_printing_process', ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesPrinting::className(), ['id' => 'kartu_proses_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['kartu_process_printing_id' => 'id']);
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
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWoColor()
    {
        return $this->hasOne(TrnWoColor::className(), ['id' => 'wo_color_id']);
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
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['kartu_process_id' => 'id']);
    }

    public function getTrnInspectingsDelivered()
    {
        return $this->getTrnInspectings()->andWhere(['status' => TrnInspecting::STATUS_DELIVERED]);
    }

    public function setNomor(){
        /*$this->setNoUrut();

        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->wo->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";*/

        $this->setNoUrut();
        $namaGreige = $this->wo->greige->nama_kain;
        $this->no = $namaGreige.'/'.$this->nomor_kartu;
    }

    public function setNomorProses(){
        $dateArr = explode('-', $this->date);
        $year = $dateArr[0];

        $namaGreige = $this->wo->greige->nama_kain;

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no_proses = "{$namaGreige}/{$noUrut}/{$yearTwoDigit}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesPrinting*/
        $lastData = self::find()
            ->select('trn_kartu_proses_printing.id, trn_kartu_proses_printing.no_urut, trn_kartu_proses_printing.date')
            ->joinWith('wo', false)
            ->where([
                'and',
                ['not', ['trn_kartu_proses_printing.no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."date") = \''.$ym.'\''),
                ['trn_wo.greige_id'=>$this->wo->greige_id]
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

    /**
     * @param string|int $processId
     * @param array $modelsMstProcessPrinting MstProcessPrinting::toArray
     * @return bool
     */
    public function isProcessDone($processId, $modelsMstProcessPrinting){
        //$processName = $modelsMstProcessPrinting['nama_proses'];
        unset($modelsMstProcessPrinting['id']);unset($modelsMstProcessPrinting['order']);unset($modelsMstProcessPrinting['max_pengulangan']);unset($modelsMstProcessPrinting['nama_proses']);unset($modelsMstProcessPrinting['created_at']);unset($modelsMstProcessPrinting['created_by']);unset($modelsMstProcessPrinting['updated_at']);unset($modelsMstProcessPrinting['updated_by']);

        /* @var $modelProcessItem KartuProcessDyeingProcess*/
        $modelProcessItem = $this->getKartuProcessPrintingProcesses()->andWhere(['process_id'=>$processId])->one();

        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        //BaseVarDumper::dump($processItems, 10, true);Yii::$app->end();

        foreach ($modelsMstProcessPrinting as $key=>$value) {
            if($value){
                if(!isset($processItems[$key]) || empty($processItems[$key])){
                    return false;
                }
            }
        }

        /*BaseVarDumper::dump([
            '$processName'=>$processName
            '$processItems'=>$processItems,
            '$modelsMstProcessPrinting'=>$modelsMstProcessPrinting
        ], 10, true);Yii::$app->end();*/

        return true;
    }

    /**
     * @return boolean
     */
    public function getIsAllProcessDone(){
        foreach (MstProcessPrinting::find()->orderBy('order ASC')->asArray()->all() as $item) {
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
        /* @var $modelProcessItem KartuProcessPrintingProcess*/
        $modelProcessItem = $this->getKartuProcessPrintingProcesses()->andWhere(['process_id'=>$processId])->one();
        if($modelProcessItem === null){
            return false;
        }

        $processItems = Json::decode($modelProcessItem->value);
        if(!isset($processItems['pengulangan'])){
            return false;
        }

        return true;
    }
}
