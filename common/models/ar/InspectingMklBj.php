<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\ar\MstK3l;

/**
 * This is the model class for table "inspecting_mkl_bj".
 *
 * @property int $id
 * @property int|null $no_urut
 * @property string|null $no
 * @property int $wo_id
 * @property int $wo_color_id
 * @property string $tgl_inspeksi
 * @property string $tgl_kirim
 * @property string $no_lot
 * @property int $jenis 1=Makloon Proses, 2=Makloon Finish, 3=Barang Jadi , 4=Fresh
 * @property int $jenis_inspek 1=Fresh Order, 2=Re-Packing
 * @property int $satuan Mengacu pada MstGreigeGroup::unitOptions()
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 * @property int $status 1=draft, 2=posted
 * @property int|null $delivered_at
 * @property int|null $delivered_by
 * @property string|null $delivery_reject_note
 * @property string|null $no_memo
 *
 * @property TrnMo $mo
 * @property TrnWo $wo
 * @property TrnWoColor $woColor
 * @property TrnMoColor $moColor
 * @property InspectingMklBjItems[] $items
 *
 * @property string $woNo
 * @property string $colorName
 * @property string $jenisName
 * @property string $satuanName
 * @property string $statusName
 * @property string $designName
 * @property string $articleName
 * @property string $greigeName
 */
class InspectingMklBj extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;const STATUS_POSTED = 2; const STATUS_DELIVERED = 3;//Diterima oleh gudang jadi
    /**
     * @return array
     */
    public static function statusOptions(){
        return [self::STATUS_DRAFT => 'Draft', self::STATUS_POSTED => 'Posted', self::STATUS_DELIVERED => 'Diterima Gudang Jadi'];
    }

    const JENIS_MAKLOON_PROSES=1; const JENIS_MAKLOON_FIINISH=2; const JENIS_BARANG_JADI=3; const JENIS_FRESH=4;

    /**
     * @return array
    */
    public static function jenisOptions(){
        return [
            self::JENIS_MAKLOON_PROSES => 'Makloon Proses',
            self::JENIS_MAKLOON_FIINISH => 'Makloon Finish',
            self::JENIS_BARANG_JADI => 'Barang Jadi',
            self::JENIS_FRESH => 'Fresh'
        ];
    }

    const FRESH_INSPEKSI = 1;
    const RE_INSPEKSI = 2;
    const HASIL_PERBAIKAN = 3;

    public static function jenisInspeksiOptions(){
        return [self::FRESH_INSPEKSI => 'Fresh Order', self::RE_INSPEKSI => 'Re-Packing', self::HASIL_PERBAIKAN => 'Hasil Perbaikan'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspecting_mkl_bj';
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
            [['wo_id', 'wo_color_id', 'tgl_inspeksi', 'tgl_kirim', 'no_lot', 'jenis', 'satuan', 'k3l_code', 'jenis_inspek'], 'required'],
            [['wo_color_id', 'jenis', 'satuan', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'default', 'value' => null],
            [['wo_id', 'wo_color_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'delivered_at', 'delivered_by'], 'integer'],
            [['tgl_inspeksi', 'tgl_kirim', 'delivery_reject_note', 'defect'], 'safe'],
            [['no_lot', 'defect'], 'string', 'max' => 255],
            [['k3l_code'], 'exist', 'skipOnError' => true, 'targetClass' => MstK3l::className(), 'targetAttribute' => ['k3l_code' => 'k3l_code']],

            [['no_memo'], 'string', 'max' => 255],

            ['status', 'default', 'value'=>self::STATUS_DRAFT],
            ['status', 'in', 'range'=>[self::STATUS_DRAFT, self::STATUS_POSTED, self::STATUS_DELIVERED]],

            ['jenis', 'in', 'range' => [self::JENIS_MAKLOON_PROSES, self::JENIS_MAKLOON_FIINISH, self::JENIS_BARANG_JADI, self::JENIS_FRESH]],
            ['jenis_inspek', 'in', 'range' => [self::FRESH_INSPEKSI, self::RE_INSPEKSI, self::HASIL_PERBAIKAN]],

            ['no_urut', 'default', 'value' => null],
            ['no_urut', 'integer'],
            // ['no_urut', 'unique', 'targetAttribute' => ['no_urut', 'jenis'], 'message' => 'No urut sudah ada untuk jenis ini.'],
            ['satuan', 'in', 'range' => [MstGreigeGroup::UNIT_YARD, MstGreigeGroup::UNIT_METER, MstGreigeGroup::UNIT_PCS, MstGreigeGroup::UNIT_KILOGRAM]],

            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['wo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWoColor::className(), 'targetAttribute' => ['wo_color_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wo_id' => 'Wo ID',
            'wo_color_id' => 'Wo Color ID',
            'tgl_inspeksi' => 'Tgl Inspeksi',
            'tgl_kirim' => 'Tgl Kirim',
            'no_lot' => 'No Lot',
            'jenis' => 'Jenis',
            'jenis_inspek' => 'Jenis Inspeksi',
            'no_memo' => 'No Memo',
            'satuan' => 'Satuan',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'status' => 'Status',
            'delivered_at' => 'Delivered At',
            'delivered_by' => 'Delivered By',
            'delivery_reject_note' => 'Delivery Reject Note',
            'colorName' => 'Color',
            'jenisName' => 'Jenis',
            'satuanName' => 'Satuan',
            'statusName' => 'Status',
            'woNo' => 'Wo No',
            'designName' => 'No. Design',
            'articleName' => 'Artikel',
            'greigeName' => 'Motif',
            'defect'=> 'Defect'
        ];
    }

    /**
     * Gets query for [[WoNo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWo()
    {
        return $this->hasOne(TrnWo::className(), ['id' => 'wo_id']);
    }

    /**
     * Gets query for [[WoNo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id'])->via('wo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(InspectingMklBjItems::className(), ['inspecting_id' => 'id']);
    }

    /**
     * Gets query for [[WoColor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWoColor()
    {
        return $this->hasOne(TrnWoColor::className(), ['id' => 'wo_color_id']);
    }

    /**
     * Gets query for [[MoColor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMoColor()
    {
        return $this->hasOne(TrnMoColor::className(), ['id' => 'mo_color_id'])->via('woColor');
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
    public function getDesignName()
    {
        return $this->mo->design;
    }

    /**
     * @return string
     */
    public function getArticleName()
    {
        return $this->mo->article;
    }

    /**
     * @return string
     */
    public function getGreigeName()
    {
        return $this->wo->greige->nama_kain;
    }

    /**
     * @return string
     */
    public function getColorName()
    {
        return $this->moColor->color;
    }

    /**
     * @return string
     */
    public function getJenisName()
    {
        return self::jenisOptions()[$this->jenis];
    }

    public function getJenisInspeksi()
    {
        return self::jenisInspeksiOptions()[$this->jenis_inspek];
    }

    /**
     * @return string
     */
    public function getSatuanName()
    {
        return MstGreigeGroup::unitOptions()[$this->satuan];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return self::statusOptions()[$this->status];
    }

    public function setNomor(){
        $this->setNoUrut();

        $dateArr = explode('-', $this->tgl_inspeksi);
        $year = $dateArr[0];
        //$month = $dateArr[1];

        switch ($this->jenis){
            case self::JENIS_MAKLOON_PROSES:
                $code = 'MP';
                break;
            case self::JENIS_MAKLOON_FIINISH:
                $code = 'MF';
                break;
            case self::JENIS_BARANG_JADI:
                $code = 'BJ';
                break;
            case self::JENIS_FRESH:
                $code = 'FR';
                break;
        }

        $noUrut = sprintf("%04s", $this->no_urut);
        $yearTwoDigit = substr($year, 2, 2);
        $this->no = "{$noUrut}/{$code}/{$yearTwoDigit}";
    }

    private function setNoUrut(){
        $ts = strtotime($this->tgl_inspeksi);
        $y = date("Y", $ts);
        //$m = date("n", $ts); //bulan satu digit 1-12
        //$ym = $y.'-'.$m;

        /* @var $lastData InspectingMklBj*/
        $lastData = self::find()
            ->where([
                'and',
                ['jenis'=>$this->jenis],
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tgl_inspeksi") = \''.$y.'\''),
                //new Expression('EXTRACT(YEAR FROM "'.self::tableName().'"."tgl_inspeksi") || \'-\' || EXTRACT(MONTH FROM "'.self::tableName().'"."tgl_inspeksi") = \''.$y.'\''),
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