<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "pfp_keluar_verpacking".
 *
 * @property int $id
 * @property int $pfp_keluar_id
 * @property int $greige_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $jenis Mengacu pada TrnPfpKeluar::jenisOptions()
 * @property int $satuan Mengacu pada MstGreigeGroup::unitOptions()
 * @property string $tgl_kirim
 * @property string $tgl_inspect
 * @property string|null $note
 * @property bool $send_to_vendor
 * @property int|null $vendor_id
 * @property int|null $wo_id
 * @property string|null $vendor_address
 * @property int $status 1=Draft, 2=Posted, 3=Approved
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property MstVendor $vendor
 * @property TrnPfpKeluar $pfpKeluar
 * @property MstGreige $greige
 * @property TrnWo $wo
 * @property PfpKeluarVerpackingItem[] $pfpKeluarVerpackingItems
 *
 * @property string $statusName
 * @property string $pfpKeluarNo
 * @property string $vendorName
 * @property string $woNo
 * @property string $jenisName
 * @property string $satuanName
 * @property string $greigeNamaKain
 */
class PfpKeluarVerpacking extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2;const STATUS_APPROVED = 3;

    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_APPROVED => 'Approved'];
    }

    /**
     * @return string
    */
    public function getStatusName(){
        return self::statusOptions()[$this->status];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pfp_keluar_verpacking';
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
            [['pfp_keluar_id', 'greige_id', 'tgl_kirim', 'tgl_inspect'], 'required'],
            [['pfp_keluar_id', 'greige_id', 'no_urut', 'jenis', 'satuan', 'vendor_id', 'vendor_address', 'wo_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['pfp_keluar_id', 'greige_id', 'no_urut', 'jenis', 'satuan', 'vendor_id', 'wo_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['tgl_kirim', 'tgl_inspect'], 'safe'],
            [['note', 'vendor_address'], 'string'],
            [['send_to_vendor'], 'boolean'],
            [['no'], 'string', 'max' => 255],
            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range'=>[self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_APPROVED]],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstVendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
            [['pfp_keluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnPfpKeluar::className(), 'targetAttribute' => ['pfp_keluar_id' => 'id']],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
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
            'pfp_keluar_id' => 'Pfp Keluar ID',
            'greige_id' => 'Greige ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'jenis' => 'Jenis',
            'satuan' => 'Satuan',
            'tgl_kirim' => 'Tgl Kirim',
            'tgl_inspect' => 'Tgl Inspect',
            'note' => 'Note',
            'send_to_vendor' => 'Kirim Ke Vendor',
            'vendor_id' => 'Vendor ID',
            'wo_id' => 'Wo ID',
            'vendor_address' => 'Vendor Address',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'statusName' => 'Status',
            'pfpKeluarNo' => 'No. PFP Keluar',
            'vendorName' => 'Vendor',
            'woNo' => 'No. WO',
            'jenisName' => 'Jenis',
            'satuanName' => 'Satuan',
            'greigeNamaKain' => 'Motif',
        ];
    }

    /**
     * @inheritDoc
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $this->send_to_vendor = $this->vendor_id !== null;

            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Vendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(MstVendor::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendor->name;
    }

    /**
     * Gets query for [[PfpKeluar]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPfpKeluar()
    {
        return $this->hasOne(TrnPfpKeluar::className(), ['id' => 'pfp_keluar_id']);
    }

    /**
     * @return string
     */
    public function getPfpKeluarNo()
    {
        return $this->pfpKeluar->no;
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

    /**
     * @return string
     */
    public function getWoNo()
    {
        return $this->wo->no;
    }

    /**
     * @return string
     */
    public function getJenisName(){
        return TrnPfpKeluar::jenisOptions()[$this->jenis];
    }

    /**
     * @return string
     */
    public function getSatuanName(){
        return MstGreigeGroup::unitOptions()[$this->satuan];
    }

    /**
     * Gets query for [[PfpKeluarVerpackingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPfpKeluarVerpackingItems()
    {
        return $this->hasMany(PfpKeluarVerpackingItem::className(), ['pfp_keluar_verpacking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * @return string
     */
    public function getGreigeNamaKain()
    {
        return $this->greige->nama_kain;
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->tgl_inspect);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);

        $this->no = $noUrut.'/'.$yearTwoDigit;
    }

    private function setNoUrut(){
        $ts = strtotime($this->tgl_inspect);
        $y = date("Y", $ts);
        //$m = date("n", $ts); //bulan satu digit 1-12

        /* @var $lastData JualExFinish*/
        $lastData = self::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tgl_inspect") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."tgl_inspect") = \''.$y.'-'.$m.'\''),
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tgl_inspect") = \''.$y.'\''),
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
