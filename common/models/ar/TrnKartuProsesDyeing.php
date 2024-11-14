<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;

/**
 * This is the model class for table "trn_kartu_proses_dyeing".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $wo_id
 * @property int|null $kartu_proses_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $asal_greige Ikut ke options TrnStockGreige
 * @property string|null $dikerjakan_oleh
 * @property string|null $lusi
 * @property string|null $pakan
 * @property string|null $note
 * @property string $date
 * @property int|null $posted_at
 * @property int|null $approved_at
 * @property int|null $approved_by
 * @property int|null $delivered_at
 * @property int|null $delivered_by
 * @property string|null $reject_notes
 * @property int $status 1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected, 6=Ganti Greige (proses gagal dan dibuat memo pengantian greige), 7=Ganti Greige Linked (sudah dibuat kartu proses turunan nya), 8=Batal
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $memo_pg
 * @property int|null $memo_pg_at
 * @property int|null $memo_pg_by
 * @property string|null $memo_pg_no
 * @property string|null $berat
 * @property string|null $lebar
 * @property string|null $k_density_lusi density_lusi konstruksi greige
 * @property string|null $k_density_pakan density_pakan konstruksi greige
 * @property string|null $lebar_preset
 * @property string|null $lebar_finish
 * @property string|null $berat_finish
 * @property string|null $t_density_lusi density_lusi target hasil jadi
 * @property string|null $t_density_pakan density_pakan target hasil jadi
 * @property string|null $handling
 * @property string|null $hasil_tes_gosok
 * @property int $wo_color_id
 * @property boolean $no_limit_item Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.
 * @property string $nomor_kartu
 *
 * @property KartuProcessDyeingProcess[] $kartuProcessDyeingProcesses
 * @property MstProcessDyeing[] $processes
 * @property TrnInspecting[] $trnInspectings
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property TrnWoColor $woColor
 * @property TrnKartuProsesDyeing $kartuProses
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItemsTubeKiri
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItemsTubeKanan
 *
 * @property boolean $isAllProcessDone
 */
class TrnKartuProsesDyeing extends \yii\db\ActiveRecord
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
        return 'trn_kartu_proses_dyeing';
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
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'kartu_proses_id', 'memo_pg_at', 'memo_pg_by','date_toping_matching'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'kartu_proses_id', 'memo_pg_at', 'memo_pg_by', 'wo_color_id','date_toping_matching'], 'integer'],
            [['note', 'reject_notes', 'memo_pg', 'memo_pg_no', 'nomor_kartu'], 'string'],
            ['berat', 'number'],
            ['no_limit_item', 'default', 'value'=>false],
            [['no', 'dikerjakan_oleh', 'lusi', 'pakan', 'lebar', 'k_density_lusi', 'k_density_pakan', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling', 'hasil_tes_gosok'], 'string', 'max' => 255],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['wo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWoColor::className(), 'targetAttribute' => ['wo_color_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],

            [['asal_greige', 'date', 'wo_color_id', 'lebar', 'lusi', 'pakan', 'k_density_lusi', 'k_density_pakan', 'nomor_kartu'], 'required'],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range'=>[self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_DELIVERED, self::STATUS_APPROVED, self::STATUS_INSPECTED, self::STATUS_GANTI_GREIGE, self::STATUS_GANTI_GREIGE_LINKED, self::STATUS_BATAL]],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['tunggu_marketing', 'toping_matching'], 'boolean'],
        ];
    }

    /*public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){
                $greigeId = $this->wo->greige_id;
                //@var $noSameExixts TrnKartuProsesDyeing[]
                $noSameExixts = self::find()->where(['nomor_kartu'=>$this->nomor_kartu])->all();
                foreach ($noSameExixts as $noSameExixt) {
                    $gid = $noSameExixt->wo->greige_id;
                    if ($this->nomor_kartu === $noSameExixt->nomor_kartu && $greigeId === $gid) {
                        $this->addError('nomor_kartu', 'Kombinasi nomor kartu dan motif sudah digunakan.');
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }*/

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
            'kartu_proses_id' => 'Kartu Proses Id',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'asal_greige' => 'Asal Greige',
            'dikerjakan_oleh' => 'Dikerjakan Oleh',
            'lusi' => 'Lusi',
            'pakan' => 'Pakan',
            'note' => 'Catatan Proses',
            'date' => 'Tanggal',
            'posted_at' => 'Posted At',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
            'delivered_at' => 'Delivered At',
            'delivered_by' => 'Delivered By',
            'reject_notes' => 'Reject Notes',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'memo_pg' => 'Memo Penggantian Greige',
            'memo_pg_at' => 'Memo PG At',
            'memo_pg_by' => 'Memo PG By',
            'memo_pg_no' => 'Memo PG No',
            'berat' => 'Berat',
            'lebar' => 'Lebar',
            'k_density_lusi' => 'Density Lusi Konstruksi Greige',
            'k_density_pakan' => 'Density Pakan Konstruksi Greige',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            't_density_lusi' => 'Density Lusi Target Hasil Jadi',
            't_density_pakan' => 'Density Pakan Target Hasil Jadi',
            'handling' => 'Handling',
            'hasil_tes_gosok' => 'Hasil Tes Gosok',
            'wo_color_id' => 'WO Color Id',
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
    public function getKartuProcessDyeingProcesses()
    {
        return $this->hasMany(KartuProcessDyeingProcess::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProcesses()
    {
        return $this->hasMany(MstProcessDyeing::className(), ['id' => 'process_id'])->viaTable('kartu_process_dyeing_process', ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['kartu_process_dyeing_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectingsDelivered()
    {
        return $this->getTrnInspectings()->andWhere(['status' => TrnInspecting::STATUS_DELIVERED]);
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
    public function getKartuProses()
    {
        return $this->hasOne(TrnKartuProsesDyeing::className(), ['id' => 'kartu_proses_id']);
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
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['kartu_process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItemsTubeKiri()
    {
        return $this->getTrnKartuProsesDyeingItems()->where(['tube'=>TrnKartuProsesDyeingItem::TUBE_KIRI]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItemsTubeKanan()
    {
        return $this->getTrnKartuProsesDyeingItems()->where(['tube'=>TrnKartuProsesDyeingItem::TUBE_KANAN]);
    }

    /**
     * @param string|int $processId
     * @param array $modelsMstProcessDyeing MstProcessDyeing::toArray
     * @return bool
     */
    public function isProcessDone($processId, $modelsMstProcessDyeing){
        unset($modelsMstProcessDyeing['id']);unset($modelsMstProcessDyeing['order']);unset($modelsMstProcessDyeing['max_pengulangan']);unset($modelsMstProcessDyeing['nama_proses']);unset($modelsMstProcessDyeing['created_at']);unset($modelsMstProcessDyeing['created_by']);unset($modelsMstProcessDyeing['updated_at']);unset($modelsMstProcessDyeing['updated_by']);

        /* @var $modelProcessItem KartuProcessDyeingProcess*/
        $modelProcessItem = $this->getKartuProcessDyeingProcesses()->andWhere(['process_id'=>$processId])->one();

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
            '$modelsMstProcessDyeing'=>$modelsMstProcessDyeing
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
        /* @var $modelProcessItem KartuProcessDyeingProcess*/
        $modelProcessItem = $this->getKartuProcessDyeingProcesses()->andWhere(['process_id'=>$processId])->one();
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

    private function setNoUrut(){
        $ts = strtotime($this->date);
        $y = date("Y", $ts);
        $m = date("n", $ts); //bulan satu digit 1-12
        $ym = $y.'-'.$m;

        /* @var $lastData TrnKartuProsesDyeing*/
        $lastData = self::find()
            ->select('trn_kartu_proses_dyeing.id, trn_kartu_proses_dyeing.no_urut, trn_kartu_proses_dyeing.date')
            ->joinWith('wo', false)
            ->where([
                'and',
                ['not', ['trn_kartu_proses_dyeing.no_urut' => null]],
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
    * Retrieves the tanggal from the KartuProcessDyeingProcess model for the given process_id.
    *
    * @return string|null The tanggal value from the KartuProcessDyeingProcess model, or null if not found.
    */
    public function getTanggalKartuProcessDyeingProcess(){
        /* @var $modelProcessItem KartuProcessDyeingProcess*/
        $model = $this->getKartuProcessDyeingProcesses()->andWhere(['process_id'=>1])->one();
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

    
    /**
    * Retrieves the latest KartuProcessDyeingProcess model for the given kartu_proses_id.
    *
    * @return KartuProcessDyeingProcess|null The latest KartuProcessDyeingProcess model, or null if not found.
    */
    public function getLatestKartuProcessDyeingProcess() {
        $model = $this->getKartuProcessDyeingProcesses()->all();
        $tanggal = [];
    
        if ($model !== null) {
            foreach ($model as $m) {
                $dcd = \yii\helpers\Json::decode($m['value']);
                if (!isset($dcd['tanggal'])) {
                    continue;
                }
                $tanggal[] = [
                    'tanggal' => $dcd['tanggal'],
                    'process_id' => $m['process_id'],
                ];
            }
        }
    
        if (count($tanggal) > 1) {
            array_multisort(array_column($tanggal, 'tanggal'), SORT_ASC, $tanggal);
        }
    
        // Mengembalikan elemen terakhir atau `null` jika `$tanggal` kosong
        return !empty($tanggal) ? end($tanggal) : null;
    }
    
    

    /**
     * Gets the resin finish process panjang_jadi.
     *
     * @return mixed|null The resin finish panjang_jadi, or null if not found.
     */
    public function getResinFinish()
    {
        $model = $this->getKartuProcessDyeingProcesses()->andWhere(['process_id'=>11])->one();
        if($model !== null){
            try {
                $model = \yii\helpers\Json::decode($model['value']);
                return isset($model['panjang_jadi']) ? $model['panjang_jadi'] : null;
            }catch (Throwable $t){
                return null;
            }
        }
        return null;
    }
    

}
