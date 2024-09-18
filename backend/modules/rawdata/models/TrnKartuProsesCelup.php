<?php

namespace backend\modules\rawdata\models;

use Yii;

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
 *
 * @property KartuProcessCelupProcess[] $kartuProcessCelupProcesses
 * @property MstProcessDyeing[] $processes
 * @property MstGreige $greige
 * @property MstGreigeGroup $greigeGroup
 * @property TrnOrderCelup $orderCelup
 * @property User $approvedBy
 * @property User $deliveredBy
 * @property TrnKartuProsesCelupItem[] $trnKartuProsesCelupItems
 */
class TrnKartuProsesCelup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_celup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_group_id', 'greige_id', 'order_celup_id', 'asal_greige', 'date', 'status', 'created_at', 'created_by'], 'required'],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'default', 'value' => null],
            [['greige_group_id', 'greige_id', 'order_celup_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'delivered_at', 'delivered_by'], 'integer'],
            [['note', 'reject_notes'], 'string'],
            [['date'], 'safe'],
            [['no', 'no_proses', 'dikerjakan_oleh', 'lusi', 'pakan', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'gramasi', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling'], 'string', 'max' => 255],
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
            'k_density_lusi' => 'K Density Lusi',
            'k_density_pakan' => 'K Density Pakan',
            'gramasi' => 'Gramasi',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            't_density_lusi' => 'T Density Lusi',
            't_density_pakan' => 'T Density Pakan',
            'handling' => 'Handling',
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
}
