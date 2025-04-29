<?php

namespace backend\modules\rawdata\models;

use Yii;
use \common\models\ar\TrnKartuProsesDyeing as KpDyeing;

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
 * @property int $asal_greige 1=Water Jet Loom, 2=Beli, 3=Rapier
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
 * @property string|null $memo_pg memo penggantian greige
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
 *
 * @property KartuProcessDyeingProcess[] $kartuProcessDyeingProcesses
 * @property MstProcessDyeing[] $processes
 * @property TrnInspecting[] $trnInspectings
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property TrnWo $wo
 * @property TrnWoColor $woColor
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 */
class TrnKartuProsesDyeing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_kartu_proses_dyeing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'asal_greige', 'date', 'status', 'created_at', 'created_by', 'wo_color_id'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'wo_color_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'wo_id', 'kartu_proses_id', 'no_urut', 'asal_greige', 'posted_at', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'memo_pg_at', 'memo_pg_by', 'wo_color_id'], 'integer'],
            [['note', 'reject_notes', 'memo_pg'], 'string'],
            [['date'], 'safe'],
            [['no', 'dikerjakan_oleh', 'lusi', 'pakan', 'memo_pg_no', 'berat', 'lebar', 'k_density_lusi', 'k_density_pakan', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan', 'handling', 'hasil_tes_gosok','nomor_kartu'], 'string', 'max' => 255],
            ['status', 'default', 'value'=>KpDyeing::STATUS_DRAFT],
            ['status', 'in', 'range'=>[KpDyeing::STATUS_DRAFT, KpDyeing::STATUS_POSTED, KpDyeing::STATUS_DELIVERED, KpDyeing::STATUS_APPROVED, KpDyeing::STATUS_INSPECTED, KpDyeing::STATUS_GANTI_GREIGE, KpDyeing::STATUS_GANTI_GREIGE_LINKED, KpDyeing::STATUS_BATAL]],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['wo_color_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWoColor::className(), 'targetAttribute' => ['wo_color_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'asal_greige' => 'Asal Greige',
            'dikerjakan_oleh' => 'Dikerjakan Oleh',
            'lusi' => 'Lusi',
            'pakan' => 'Pakan',
            'note' => 'Note',
            'date' => 'Date',
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
            'memo_pg' => 'Memo Pg',
            'memo_pg_at' => 'Memo Pg At',
            'memo_pg_by' => 'Memo Pg By',
            'memo_pg_no' => 'Memo Pg No',
            'berat' => 'Berat',
            'lebar' => 'Lebar',
            'k_density_lusi' => 'K Density Lusi',
            'k_density_pakan' => 'K Density Pakan',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            't_density_lusi' => 'T Density Lusi',
            't_density_pakan' => 'T Density Pakan',
            'handling' => 'Handling',
            'hasil_tes_gosok' => 'Hasil Tes Gosok',
            'wo_color_id' => 'Wo Color ID',
            'nomor_kartu' => 'Nomor Kartu',
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
     */
    public function getProcesses()
    {
        return $this->hasMany(MstProcessDyeing::className(), ['id' => 'process_id'])->viaTable('kartu_process_dyeing_process', ['kartu_process_id' => 'id']);
    }

    /**
     * Gets query for [[TrnInspectings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['kartu_process_dyeing_id' => 'id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
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
     * Gets query for [[WoColor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWoColor()
    {
        return $this->hasOne(TrnWoColor::className(), ['id' => 'wo_color_id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['kartu_process_id' => 'id']);
    }
}
